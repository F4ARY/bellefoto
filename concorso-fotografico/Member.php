<?php
require_once 'Util.php';
class Member
{
    private $ds;

    function __construct()
    {
        require_once "DBController.php";
        $this->ds = new DBController();
    }

    public function isEmailExists($email)
    {
        $query = 'SELECT * FROM members where member_email = ?';
        $paramType = 's';
        $paramValue = array(
            $email
        );
        $resultArray = $this->ds->select($query, $paramType, $paramValue);
        $count = 0;
        if (is_array($resultArray)) {
            $count = count($resultArray);
        }
        if ($count > 0) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }

    public function registerMember()
    {
        $token = md5(uniqid($_POST['email'], true));
        $token = substr($token, 0, 16 );

        $response = array(
            "status" => "success",
            "message" => "You have registered successfully.",
            "mail" => $_POST['email'],
            "token" => $token
        );
        $isEmailExists = $this->isEmailExists($_POST["email"]);
        if ($isEmailExists) {
            $response = array(
                "status" => "error",
                "message" => "Email already exists.",
            );
        } else {
            if (!empty($_POST["signup-password"])) {
                // PHP's password_hash is the best choice to use to store passwords
                // do not attempt to do your own encryption, it is not safe
                $hashedPassword = password_hash($_POST["signup-password"], PASSWORD_DEFAULT);
            }
            $email = $_POST['email'];
            $profile_picture = "default.png";
            if (!empty($_FILES["profile-picture"]["name"])) {
                $info = pathinfo($_FILES["profile-picture"]["name"]);
                $ext = ".".$info["extension"];
                $profile_picture = $email.$ext;
                $target = "pfp/".$profile_picture;
                move_uploaded_file($_FILES["profile-picture"]["tmp_name"], $target);
            }
            $query = 'INSERT INTO members (member_surname, member_name, member_password, member_email, member_token, member_profile_picture) VALUES (?, ?, ?, ?, ?, ?)';
            $paramType = 'ssssss';
            $paramValue = array(
                $_POST["surname"],
                $_POST["name"],
                $hashedPassword,
                $email,
                $token,
                $profile_picture
            );
            $memberId = $this->ds->insert($query, $paramType, $paramValue);

            if(!empty($memberId)) {
                $response = array(
                    "status" => "success",
                    "message" => "You have registered successfully.",
                    "mail" => $email,
                    "token" => $token
                );
            }
        }
        return $response;
    }

    public function getMember($email)
    {
        $query = 'SELECT * FROM members where member_email = ?';
        $paramType = 's';
        $paramValue = array(
            $email
        );
        $memberRecord = $this->ds->select($query, $paramType, $paramValue);
        return $memberRecord;
    }

    public function getId($email)
    {
        $query = 'SELECT member_id FROM members where member_email = ?';
        $paramType = 's';
        $paramValue = array(
            $email
        );
        $memberRecord = $this->ds->select($query, $paramType, $paramValue);
        return $memberRecord[0]['member_id'];

    }

    public function getEmailByID($id)
    {
        $query = 'SELECT member_email FROM members where member_id = ?';
        $paramType = 's';
        $paramValue = array(
            $id
        );
        $memberRecord = $this->ds->select($query, $paramType, $paramValue);
        return $memberRecord[0]['member_email'];
    }
  
    public function getFoto($email)
    {
        $id = $this->getId($email);
        $query = 'SELECT * FROM photo where member_id = ?';
        $paramType = 'i';
        $paramValue = array(
            $id
        );
        $memberRecord = $this->ds->select($query, $paramType, $paramValue);
        return $memberRecord;

    }

    public function verify($email, $token)
    {
        $util = new Util();
        $query = 'SELECT * FROM members where member_email = ? and member_token = ?';
        $paramType = 'ss';
        $paramValue = array(
            $email,
            $token
        );
        $ver = $this->ds->select($query, $paramType, $paramValue);
        if($ver != null) {
            $query = "UPDATE members SET member_verified = 1 WHERE member_token = ? AND member_email = ? ;";
            $this->ds->update($query, 'ss', array($token, $email));
            return true;

        }

        return false;

    }

    public function caricaImmagine($email, $img, $desc)
    {
        $response = null;
        $id = $this->getId($email);
        $util = new Util();
        $img_url = $img['tmp_name'];
        //Prendo info exif
        $exif = @exif_read_data($img_url, 0, true);
        if($exif == false)
        {
            $data = date('Y-m-d H:i:s');
            $peso = 0;
            $alt = 0;
            $larg = 0;

        }
        else
        {
            //Data file
            $data = $exif['FILE']['FileDateTime'];
            $data = date('Y-m-d H:i:s', $data);
            //Peso file
            $peso = $util->formatBytes($exif['FILE']['FileSize']);
            //Altezza file
            $alt = $exif['COMPUTED']['Height'];
            //Lunghezza file
            $larg = $exif['COMPUTED']['Width'];
        }

        //Geotag
        $imgLocation = $util->get_image_location($img_url);

        $imgLng = 0;
        $imgLat = 0;

        if($imgLocation != NULL)
        {
            //latitude & longitude
            $imgLat = $imgLocation['latitude'];
            $imgLng = $imgLocation['longitude'];

        }

        if (!empty($img["name"])) {
            $nome = $img['name'];
            $ext = explode(".", $nome)[1];
            $query = 'SELECT file_name FROM photo';
            $ver = $this->ds->runBaseQuery($query);

            if($ver != null)
            {
                    for ($i = 0; $i < count($ver); $i++)
                    {
                        $rand = md5(uniqid($email, true));
                        $unifier = substr($rand, 0, 8);
                        if(strcmp($ver[$i]['file_name'], $nome) == 0)
                        {
                            $nome = $nome.$unifier.".".$ext;
                            $i = -1;
                        }
                    }
            }
            $target = "imgs/".$nome;
            move_uploaded_file($img["tmp_name"], $target);
        }

        $query = 'INSERT INTO photo (description, member_id, file_name, height, width, occupazione, lat, lng, data_scatto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $paramType = 'sisiisdds';
        $paramValue = array(
            $desc,
            $id,
            $nome,
            $alt,
            $larg,
            $peso,
            $imgLat,
            $imgLng,
            $data
        );
        $memberId = $this->ds->insert($query, $paramType, $paramValue);
        if(!empty($memberId)) {
            $response = array(
                "status" => "success",
                "message" => "Immagine caricata!",
            );
        }
        return $response;
    }

    public function cancellaImmagine($checkbox)
    {
        $response = null;

        foreach ($checkbox as $id)
        {
            $query = 'SELECT file_name FROM photo where photo_id = ?';
            $paramType = 'i';
            $paramValue = array(
                $id
            );
            $ver = $this->ds->select($query, $paramType, $paramValue);
            unlink("imgs/".$ver[0]['file_name']);
            $query = 'DELETE FROM photo WHERE photo_id = ?';
            $paramType = 'i';
            $paramValue = array(
                $id
            );
            $memberId = $this->ds->update($query, $paramType, $paramValue);
        }

        $response = array(
            "status" => "success",
            "message" => "Immagine cancellata!",
        );

        return $response;
    }

    public function editImmagine($checkbox, $desc)
    {
        $response = null;

        foreach ($checkbox as $id)
        {
            $query = 'UPDATE photo SET description = ? WHERE photo_id = ?';
            $paramType = 'si';
            $paramValue = array(
                $desc,
                $id
            );
            $memberId = $this->ds->update($query, $paramType, $paramValue);
        }
        $response = array(
            "status" => "success",
            "message" => "Immagine aggiornata!",

        );

        return $response;
    }

    public function loginMember()
    {
        $memberRecord = $this->getMember($_POST["email"]);
        $loginPassword = 0;
        if (!empty($memberRecord)) {
            if (! empty($_POST["login-password"])) {
                $password = $_POST["login-password"];
            }
            $hashedPassword = $memberRecord[0]["password"];
            $loginPassword = 0;
            if (password_verify($password, $hashedPassword)) {
                $loginPassword = 1;
            }
        } else {
            $loginPassword = 0;
        }
        if ($loginPassword == 1) {
            // login sucess so store the member's name in
            // the session
            session_start();
            $_SESSION["email"] = $memberRecord[0]["email"];
            session_write_close();
            $url = "./home.php";
            header("Location: $url");
        } else if ($loginPassword == 0) {
            $loginStatus = "Invalid e-mail or password.";
            return $loginStatus;
        }
    }
}