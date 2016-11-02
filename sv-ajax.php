<?php
  set_time_limit(0);

  if (isset($_POST['keywords']) && (isset($_POST['loc']) && (isset($_POST['apikey']))))
  {
      $keywords = explode("\n",$_POST['keywords']);
      $kws = implode('|',$keywords);
      $loc = $_POST['loc'];
      $apikey = $_POST['apikey'];
      //echo $kws;
      $response = array("response" => "WIN", "keywords" => grepwords($apikey,$kws,$loc));

  }
  elseif(isset($_POST['keywords']) && (isset($_POST['apikey'])))
  {
    //  echo "if isset keywrods - is this happening?";
      $keywords = explode("\n",$_POST['keywords']);
      $kws = implode('|',$keywords);
      $apikey = $_POST['apikey'];
      //echo $kws;
      $response = array("response" => "WIN", "keywords" => grepwords($apikey,$kws));

  }
  else{
    $response = array("response"=>"FAIL","message"=>"No Keywords Set");
  }
  echo json_encode($response);

// add api key var
  function grepwords($apikey,$kw,$loc="",$wait=0)
  {
    if ($loc != "")
    {
      $url = 'http://api.grepwords.com/lookup?apikey='.$apikey.'&q='.urlencode($kw).'&loc='.urlencode($loc);
    }
    else{
      $url = 'http://api.grepwords.com/lookup?apikey='.$apikey.'&q='.urlencode($kw);
    }

  	sleep($wait);
  	$data = crawl($url,1);

  	if ($data['INF']['http_code'] == 200)
  	{
  		$data = json_decode($data['EXE'], true);
      return (grepWordsResponseHandler($data,$loc));
  	}
  	else{
      $keywordData['keyword'] = 'API ERROR';
      $keywordData['cpc'] ='API ERROR';
      $keywordData['searchvolume'] =  $data['INF']['http_code'];
      return $keywordData;
  	}
  }

  function grepWordsResponseHandler($data,$loc)
  {
    $keywordCount = count($data);
      if ($loc != "")
      {
        foreach($data['keywords'] as $keyword)
        {
        $keywordData[] = array(
          'keyword' =>  $keyword['keyword'],
          'searchvolume' => $keyword['lms'],
          'cpc' => $keyword['cpc'],
          'm1' => $keyword['m1'],
          'm2' => $keyword['m2'],
          'm3' => $keyword['m3'],
          'm4' => $keyword['m4'],
          'm5' => $keyword['m5'],
          'm6' => $keyword['m6'],
          'm7' => $keyword['m7'],
          'm8' => $keyword['m8'],
          'm9' => $keyword['m9'],
          'm10' => $keyword['m10'],
          'm11' => $keyword['m11'],
          'm12' => $keyword['m12'],
          'country' => $keyword['country'],
          'estClicks' => $keyword['Estimated_clicks'],
          'estImp' => $keyword['Estimated_Impressions'],
          'estCTR' => $keyword['Estimated_CTR'],
          'estAvgCPC' => $keyword['Estimated_Average_CPC'],
          'estAvgPos' => $keyword['Estimated_Average_Position']
        );
        }
      }
      else {
        for ($i=0;$i<$keywordCount;$i++)
        {
          $keywordData[$i] = array(
            'keyword' =>  $data[$i]['keyword'],
            'searchvolume' => $data[$i]['lms'],
            'cpc' => $data[$i]['cpc'],
            'm1' => $data[$i]['m1'],
            'm2' => $data[$i]['m2'],
            'm3' => $data[$i]['m3'],
            'm4' => $data[$i]['m4'],
            'm5' => $data[$i]['m5'],
            'm6' => $data[$i]['m6'],
            'm7' => $data[$i]['m7'],
            'm8' => $data[$i]['m8'],
            'm9' => $data[$i]['m9'],
            'm10' => $data[$i]['m10'],
            'm11' => $data[$i]['m11'],
            'm12' => $data[$i]['m12'],
            'country' => 'US'
          );
        }

    }
    return $keywordData;
  }


  function crawl($url,$mode=0) // Grab a page's source by URL
  {
  	$ch = curl_init();
  	curl_setopt ($ch, CURLOPT_URL, $url);
  	curl_setopt($ch, CURLOPT_HEADER, 0);
  	curl_setopt($ch, CURLOPT_NOBODY, 0);
  	curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
  	curl_setopt($ch, CURLOPT_NOPROGRESS, 1);
  	curl_setopt($ch, CURLOPT_FAILONERROR, 1);
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  	curl_setopt ($ch, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; GV1; .iPullRank)");
      if ($mode==1)
      {
  	    $result['EXE'] = curl_exec ($ch);
  	    $result['INF'] = curl_getinfo($ch);
  	    $result['ERR'] = curl_error($ch);
      }
      else{
      	$result = curl_exec ($ch);
      	return trim($result);
      }
  	curl_close($ch);
  	return $result;
  }
