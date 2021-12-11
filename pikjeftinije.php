<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OLX Upload</title>
    <style>
        body{
            font-family:'Roboto',sans-serif;
        }
        input{
            padding: 6px;
            margin-bottom: 5px;
            font-family:'Roboto',sans-serif;
        }
        
        .button-67 {
  align-items: center;
  background: #f5f5fa;
  border: 0;
  border-radius: 8px;
  box-shadow: -10px -10px 30px 0 #fff,10px 10px 30px 0 #1d0dca17;
  box-sizing: border-box;
  color: #2a1f62;
  cursor: pointer;
  display: flex;
  font-family: "Cascadia Code",Consolas,Monaco,"Andale Mono","Ubuntu Mono",monospace;
  font-size: 1rem;
  justify-content: center;
  line-height: 1.5rem;
  padding: 15px;
  position: relative;
  text-align: center;
  transition: .2s;
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  white-space: pre;
  width: max-content;
  word-break: normal;
  word-spacing: normal;
}

.button-67:hover {
  background: #f8f8ff;
  box-shadow: -15px -15px 30px 0 #fff, 15px 15px 30px 0 #1d0dca17;
}

@media (min-width: 768px) {
  .button-67 {
    padding: 24px;
  }
}

  #button{
    margin-left: 700px;
    margin-top: 12px;

  }
    </style>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>

<?php
include "simple_html_dom.php";

function str_word_count_utf8($str) {
  return count(preg_split('~[^\p{L}\p{N}\']+~u',$str));
}



function suggest_olx_category($product_title){
  $count = str_word_count_utf8($product_title);

  if($count == 1){
    $product_title .= " " . $product_title;
  }
  
  $titleEncoded= rawurlencode($product_title);
  $link= 'https://www.olx.ba/objava/predloziKat?naziv=' . $titleEncoded;

  $data=file_get_contents($link);
  $suggestions = json_decode($data);
  if($suggestions->status == 1) {
      $reg = '/[0-9]+/';
      $suggested_categories=[];
      //$fobidden_chars = array('&', ';', ' ', ':', '(', ')', ',', '=', '_', '/', 'č', 'Č', 'ć', 'Ć', 'Š', 'š', 'ž', 'Ž', 'Đ', 'đ');
      
      //print_r($matches);//die;
      foreach ($suggestions->prijedlozi as $suggestion){
          //$match = htmlspecialchars($suggestion);
          //$match= str_replace($fobidden_chars, '', $match);
          //error_log(print_r($match, true));

          preg_match_all($reg, $suggestion, $finalMatch);
          array_push($suggested_categories, $finalMatch[0][1]);
      }

      return $suggested_categories[0];
  } else {
      return '369';
  }
}

if(isset($_POST['submit']) && !empty($_POST['submit']) && isset($_POST['name']) && !empty($_POST['name'])) {

$prvi_dio_pretrage = 'https://www.olx.ba/pretraga?trazilica=+';
$drugi_dio_pretraga = '&kategorija=';
$treci_dio_pretraga = '&id=1&stanje=0&vrstapregleda=tabela&sort_order=desc';
$cetvrti_dio_pretrage = '&kanton=';
$peti_dio_pretrage = '&grad%5B%5D=';
$naziv_proizvoda = str_replace(" ", "+", $_POST['name']);
$kategorija = suggest_olx_category($_POST['name']);
$url_pretrage = $prvi_dio_pretrage . $naziv_proizvoda . $drugi_dio_pretraga . $kategorija . $treci_dio_pretraga;

if (isset($_POST['kanton']) && !empty($_POST['kanton']) ){
  $url_pretrage .= $cetvrti_dio_pretrage . $_POST['kanton'];
}

if (isset($_POST['city'])&& !empty($_POST['city']) ){
  $url_pretrage .= $peti_dio_pretrage . $_POST['city'];
}




$response = file_get_html($url_pretrage);

echo $response;

$lista_rezultata = $response->find('div[id="rezultatipretrage"]')[0];
$prvi_artikal = $lista_rezultata->children(0); //prvi artikal u div-u
$link_ka_proizvodu = $prvi_artikal->children(1)->href; //link ka proizvodu
$naziv = $prvi_artikal->find('div[class="naslov"]')[0]->find('div[class="pna"]')->plaintext; //naziv proizvoda
$cijena = $prvi_artikal->find('div[class="cijena"]')[0]->find('div[class="datum"]')->children(0)->plaintext; //cijena kao string

//potrebni podaci ime artikla, imejl, period osvezavanja, kanton/regija
}


?>
<div style="text-align: center;">
    <h1>Unesite podatke: </h1>
    <form enctype="multipart/form-data" action="pikjeftinije.php" method="POST">
    <!--Enter category URL: <input type="text" name="cat-url"> <br>-->
        Naziv proizvoda: <input name="name" type="text" /> <br>
          <?php
           
          
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.olx.ba/zadnje");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($ch);

            if (!$response) {
              die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
            }

            $cities_data = str_get_html($response);
            $city_codes = $cities_data->find('#kanton');

            $city_codes[0]->onchange = "getCities(this)";

            echo $city_codes[0] . '<br>';

            curl_close($ch);
          ?>
<br>
          <select name="city" id="city">

          </select>
          <br>
          <br>
          <label for="email">Unesite Vas Email:</label>
          <input type="email" id="email" name="email">

          <br>

          <label for="cars">Izaberite period provjere:</label>

          <select name="vrijeme" id="1h">
          <option value="2h">2h</option>
          <option value="3h">3h</option>    
          <option value="6h">6h</option>
          <option value="12h">12h</option>
          <option value="24h">24h</option>
          </select>

          <br>
        <input type="submit" name='submit' value="Sačuvaj Promjene" id="button" class="button-67"/>
    </form>
</div>

<script>
  function getCities(element) {

    $.ajax({
				type: "POST",
				url: "./get_cities.php",
				data: "kanton=" + element.value,
				statusCode: {
					200: function(text) {
            var citiesDocument = document.createElement('html');

            citiesDocument.innerHTML = text;

            var selectCity = document.getElementById("city");
            var cityParagraphs = citiesDocument.querySelectorAll('p');

            var cNode = selectCity.cloneNode(false);
            selectCity.parentNode.replaceChild(cNode, document
    						.getElementById(selectCity.id)); //cheap & dirty clear children from node

            for(let cityParagraph of cityParagraphs) {
              var cityOption = document.createElement('option');

              cityOption.value = cityParagraph.querySelector('input').value;
              cityOption.innerText = cityParagraph.querySelector('label').innerText;

              cNode.appendChild(cityOption);
            }
					}
				},
				dateType: "html",
				cache: false
			});
  }
</script>
</body>