<!DOCTYPE html>
<html lang="en">
<head>
    <title>Generatore Codice Fiscale</title>
</head>
<body>

    <form action="generatore_cod_fisc.php" method="get">
        Nome: <input type="text" name="nome"><br><br>
        Cognome: <input type="text" name="cognome"><br><br>
        Giorno: <input type="date" name="giorno"><br><br>
        Sesso: <select name="sesso">
            <option value = "M">M</option>
            <option value = "F">F</option>
        </select><br><br>
        Luogo di nascita: <input type="text" name="comune"><br><br>
    <input type="submit">
    </form>

    <?php
        // input variabili codice fiscale
        $nome = strtoupper($_GET["nome"]);
        $cognome = strtoupper($_GET["cognome"]);
        $data = $_GET["giorno"];
        $sesso = $_GET["sesso"];
        $comune = ucwords(strtolower($_GET["comune"]));
        //divisione data in anno mese e giorno
        $date = explode("-", $data);
        $anno = $date[0];
        $mese = $date[1];
        $giorno = $date[2];
        
        //lettere alfabeto per creare il codice del nome e del cognome per il codice fiscale
        $consonanti = ["B","C","D","F","G","H","J","K","L","M","N","P","Q","R","S","T","V","W","X","Y","Z"];
        $vocali = ["A","E","I","O","U"];

        // COGNOME

        //inizializzazione codice cognome
        $codiceCognome = "";

        //ciclo per controllare le consonanti del cognome da aggiungere al codice fiscale
        for($i = 0; $i < strlen($cognome); $i++){
            for($j = 0; $j < 21; $j++){
                if($cognome[$i] == $consonanti[$j] && strlen($codiceCognome) < 3){
                    $codiceCognome .= $cognome[$i];
                }
            }
        }


        //ciclo per controllare le vocali del cognome da aggiungere al codice fiscale
        if(strlen($codiceCognome) < 3){
            for($i = 0; $i < strlen($cognome); $i++){
                for($j = 0; $j < 5; $j++){
                    if($cognome[$i] == $vocali[$j] && strlen($codiceCognome) < 3){
                        $codiceCognome .= $cognome[$i];
                    }
                }
            }
        }

        // NOME

        // inizializzazione codice nome e contatore delle consonanti
        $codiceNome = "";
        $cont_consonanti = 0;

        // ciclo per contare le consonanti del nome
        for($i = 0; $i < strlen($nome); $i++){
            for($j = 0; $j < 21; $j++){
                if($nome[$i] == $consonanti[$j]){
                    $cont_consonanti++;
                }

            }
        }

        //controllo delle consonanti(se maggiori di tre, il codice prende la prima, la terza e la quarta)
        $istrue = false;
        if ($cont_consonanti > 3) {
            $istrue = true;    
        }
        
        //ciclo che controlla le consonanti da aggiungere al codice fiscale
        for($i = 0; $i < strlen($nome); $i++){
            for($j = 0; $j < 21; $j++){
                if($nome[$i] == $consonanti[$j] && strlen($codiceNome) < 3){
                    $codiceNome .= $nome[$i];
                }
                if($istrue && strlen($codiceNome) == 2){
                    $istrue = false;
                    $codiceNome = substr($codiceNome, 0, strlen($codiceNome)-1);
                }
                
            }
        }

        //ciclo che controlla le vocali da aggiungere al codice fiscale
        if(strlen($codiceNome) < 3){
            for($i = 0; $i < strlen($nome); $i++){
                for($j = 0; $j < 5; $j++){
                    if($nome[$i] == $vocali[$j] && strlen($codiceNome) < 3){
                        $codiceNome .= $nome[$i];
                    }
                }
            }
        }
        
        // controllo se non ci sono abbastanza lettere nel nome
        while(strlen($codiceNome) < 3){
            $codiceNome .= "X";
        }

        // ANNO

        //inizializzazione codice anno
        $codiceAnno = "";

        //aggiunta delle ultime 2 cifre dell'anno di nascita al codice dell'anno
        for($i = strlen($anno)-2; $i < strlen($anno); $i++){
            $codiceAnno .= $anno[$i];
        }

        // MESE

        //array associativo che associa ad ogni mese la lettera corrispondente per il codice fiscale
        $associazioneMesi = 
        ["01" => "A", "02" => "B", "03" => "C", "04" => "D", "05" => "E", "06" => "H", "07" => "L", "08" => "M", "09" => "P", "10" => "R", "11" => "S", "12" => "T"];

        $codiceMese = "";

        $codiceMese = $associazioneMesi[$mese];


        // GIORNO E SESSO

        //impostazione del giorno di nascita insieme al sesso della persona
        if($sesso == "M"){
            $codiceGiorno = $giorno;
        }else{
            $codiceGiorno = ((int)$giorno + 40);
        }
        
        // COMUNE

        //controllo nel file "listacomuni.txt" ed estrazione del corrispondente codice catastale 
        $codiceComune = 0;
        $cont = 0;
        if (!$p_file = fopen("listacomuni.txt","r")) {
            echo "Spiacente, non posso aprire il file della lista dei comuni.txt";
        } else {
            while(!feof($p_file)){
                $linea = fgets($p_file, 255);
                    if(substr($linea,0,strlen($comune)) == $comune){
                        $codiceComune = substr($linea,-6,-2);
                    }
                }
            }
        
        fclose($p_file);

        //unione del codice
        $code = "$codiceCognome$codiceNome$codiceAnno$codiceMese$codiceGiorno$codiceComune";

        // CONTROLLO

        // controllo dell'intero codice fiscale per calcolare l'ultimo carattere di controllo
        if (strlen($code) == 15) {
          $oddsvalues = 
          [ "0" => "1", "1" => "0", "2" => "5", "3" => "7", "4" => "9", "5" => "13", "6" => "15", "7" => "17", "8" => "19", "9" => "21", "A" => "1", "B" => "0", "C" => "5", "D" => "7", "E" => "9", "F" => "13", "G" => "15", "H" => "17", "I" => "19", "J" => "21", "K" => "2", "L" => "4", "M" => "18", "N" => "20", "O" => "11", "P" => "3", "Q" => "6", "R" => "8", "S" => "12", "T" => "14", "U" => "16", "V" => "10", "W" => "22", "X" => "25", "Y" => "24", "Z" => "23"
          ];

          
          $evensvalues = 
          [ "0" => "0", "1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "6" => "6", "7" => "7", "8" => "8", "9" => "9", "A" => "0", "B" => "1", "C" => "2", "D" => "3", "E" => "4", "F" => "5", "G" => "6", "H" => "7", "I" => "8", "J" => "9", "K" => "10", "L" => "11", "M" => "12", "N" => "13", "O" => "14", "P" => "15", "Q" => "16", "R" => "17", "S" => "18", "T" => "19", "U" => "20", "V" => "21", "W" => "22", "X" => "23", "Y" => "24", "Z" => "25"
          ];

          // somma dei valori con indice pari e dispari
          $sum = 0;
          for($i = 0; $i < 15; $i++){
            if(($i+1) % 2 == 0) 
              $sum+=$evensvalues[$code[$i]];
            else
              $sum+=$oddsvalues[$code[$i]];
          }
          $remainder = $sum%26;

          $controlchar = [ "0" => "A", "1" => "B", "2" => "C", "3" => "D", "4" => "E", "5" => "F", "6" => "G", "7" => "H", "8" => "I", "9" => "J", "10" => "K", "11" => "L", "12" => "M", "13" => "N", "14" => "O", "15" => "P", "16" => "Q", "17" => "R", "18" => "S", "19" => "T", "20" => "U", "21" => "V", "22" => "W", "23" => "X", "24" => "Y", "25" => "Z"
          ];


        }

        //stampa dell'intero codice fiscale corrispondente
        echo "<br>Codice fiscale: $code"."$controlchar[$remainder]";

    ?>
    
</body>
</html>