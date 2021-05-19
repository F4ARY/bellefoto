# CONCORSO FOTOGRAFICO

L’azienda “Belle Foto srl” commissiona la creazione di un sito per la gestione di un concorso fotografico.

La partecipazione al concorso è riservata agli utenti registrati.

Per ogni partecipante è necessario memorizzare: Cognome, Nome, email, password, immagine profilo.

La registrazione avviene tramite un apposito form in cui vengono chieste le informazioni necessarie (per la password chiedere conferma). Al termine dell’immissione dei dati, l’attivazione del profilo avviene dopo conferma tramite token inviato tramite email.

Il login deve avvenire tramite immissione dell’email (univoca) e password (memorizzata in modalità criptata nel database); è previsto inoltre la funzionalità “remember me” tramite una checkbox nel form di login.

Dopo essersi correttamente autenticati, gli utenti possono:

- Gestire le proprie foto: è previsto il caricamento (con indicazione di una descrizione testuale), la
rimozione delle foto e la modifica della loro descrizione.

- Visualizzare le foto degli altri utenti, filtrandole in base a una parola chiave presente nella
descrizione delle foto.
	o Per ogni foto vengono visualizzate:
		- le informazioni di scatto(se presenti) e le dimensioni (altezza, larghezza, occupazione)
		- Nel caso la foto contenga geotag, visualizzare una mappa con il marker del luogo e la data/ora in cui è stata scattata.
		- Il numero di valutazioni e la valutazione media.
		- I commenti fatti dagli utenti

- Dare un voto a una o più foto attribuendone un voto da 1 a 5. (non è possibile votare le proprie
foto)

- Scrivere un commento ad una foto.

- Inviare un messaggio privato all’autore di una foto

- Segnalare una foto come inappropriata

- Visualizzare i propri messaggi privati

Una sezione del sito accessibile solamente all’amministratore consentirà di:

- Visualizzare le informazioni dei partecipanti al concorso

- Visualizzare le segnalazioni delle foto considerate inappropriate. Dopo averle visualizzate, l’amministratore potrà nascondere la foto dalla sezione pubblica segnalando la violazione al partecipante (tramite messaggistica interna) e inibendo il login per 24 ore (events), ignorare e cancellare la segnalazione, espellere il partecipante al concorso (viene inibito il login segnalando l’esclusione tramite email e tutte le foto del partecipante vengono nascoste dalla sezione pubblica)

- Visualizzare lo storico di tutte le votazioni (triggers)

- Visualizzare lo storico di tutte le operazioni svolte dai partecipanti (triggers)

- Visualizzare tramite un istogramma i partecipanti che hanno avuto il maggior numero di votazioni (cumulare le valutazioni delle singole foto)

- Produrre in un file PDF l’esito del concorso: stampare un tabulato con la classifica delle 5 foto con la media voto maggiore e il relativo fotografo (con foto profilo).




13	1.      database<br/>
	  2.      form di registrazione<br/>
2	    2.1.   conferma password (deve essere criptata)<br/>
2	    2.2.   upload immagine del profilo (blob)<br/>
3	    2.3.   invio email con generazione token<br/>
3	    2.4.   controllo del token e abilitazione partecipante<br/>
1	    2.5.   controllo email univoca<br/>
	  3.      form di login<br/>
2	    3.1.   controllo se è abilitato<br/>
3	    3.2.   controllo credenziali utente<br/>
6	    3.3.   remember me<br/>
	4.      funzionalità partecipante (ajax)<br/>
3	    4.1.   caricamento foto (gestione omonimie)<br/>
2	    4.2.   modifica foto<br/>
2	    4.3.   cancellazione foto<br/>
	    4.4.   visualizzazione foto altri utenti<br/>
4	      4.4.1.filtro con autosuggestion descrizione<br/>
2	      4.4.2.informazioni di scatto (exif)<br/>
2	      4.4.3.dimensioni<br/>
7	      4.4.4.geotag con visualizzazione mappa<br/>
2	      4.4.5.data di scatto<br/><br/>
4	      4.4.6.numero di votazioni<br/>
4	      4.4.7.valutazione media<br/>
4	      4.4.8.commenti<br/>
6	  4.5.   dare un voto ad una foto (non può votare le proprie foto, non può votare più la volte la stessa foto di altri utenti)<br/>
5	  4.6.   scrivere commenti ad una foto<br/>
4	  4.7.   inviare messaggio ad autore foto<br/>
4	  4.8.   Segnalare una foto come inappropriata<br/>
5	  4.9.   Visualizzare i propri messaggi privati (possibilità di cancellarli)<br/>
	  5.      form login amministratore<br/>
3	  5.1.   controllo password amministratore (solo password)<br/>
3	  5.2.   visualizzare l'elenco dei partecipanti al concorso<br/>
	  6.      visualizzazione elenco delle segnalazioni delle foto inappropriate<br/>
3	  6.1.   cancellazione segnalazione<br/>
	  6.2.   ammonimento<br/>
2	    6.2.1.disabilitazione foto segnalata<br/>
4	    6.2.2.sospensione login per 24 ore<br/>
3	    6.2.3.notifica sospesione tramite email<br/>
	  6.3.   esclusione utente<br/>
2	    6.3.1.disabilitazione di tutte le foto<br/>
2	    6.3.2.inibizione login utente<br/>
3	    6.3.3.notifica esclusione tramite email<br/>
3	  6.4.   visualizzazione elenco votazioni<br/>
	  6.5.   visualizzazione storico delle operazioni sulle foto (utente - descrizione operazione)<br/>
2   	6.5.1.inserimento nuova foto<br/>
2	    6.5.2.modifica descrizione foto<br/>
2	    6.5.3.cancellazione foto<br/>
5	  7.      visualizzane istogramma utenti con maggior numero di votazioni<br/>
6 	8.      creazione PDF con risultato concorso<br/>
