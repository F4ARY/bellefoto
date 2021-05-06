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

    public function getVerified($name)
    {
        $query = 'SELECT member_verified FROM members where member_name = ?';
        $paramType = 's';
        $paramValue = array(
            $name
        );
        $ver = $this->ds->select($query, $paramType, $paramValue);
        return $ver;
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