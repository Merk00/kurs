<?php
            include "simple_html_dom.php";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.olx.ba/ajax/gradovi?kanton=" . $_POST['kanton']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($ch);

            if (!$response) {
            die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
            }

            $cities_data = str_get_html($response);

            $city_codes = $cities_data->find('p');

            foreach ($city_codes as $key => $value) {
            echo $value . '<br>';
            }

            curl_close($ch);
        
          ?>