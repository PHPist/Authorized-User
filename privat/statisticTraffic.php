<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Документ без названия</title>
</head>

<body>
<?
include('DataJson.php');

$resursURL = 'gresso.com';

$dataArray = json_decode($DataJSON);
$statData =$dataArray->data;


/*
echo '<pre>';
print_r($statData);
echo '</pre>';
*/

$tmp = 0;
foreach ($statData as $statDataPrint){
	if (strpos($statDataPrint->url, $resursURL)){
		$summData['denial'] = $summData['denial']+$statDataPrint->denial; //отказы
		$summData['visits'] = $summData['visits']+$statDataPrint->visits;//кол. визитов
		$summData['page_views'] = $summData['page_views']+$statDataPrint->page_views;//кол. просмотров
		$summData['visit_time'] = $summData['visit_time']+$statDataPrint->visit_time;//время визита
		$summData['depth'] = $summData['depth']+$statDataPrint->depth;//глубина
				
		$sResult[$tmp] = $statDataPrint;
		$tmp++;
		}
	}

	
	$summData['visit_time'] = $summData['visit_time']/$tmp;
	$summData['depth'] = $summData['depth']/$tmp;
	
	
	echo '<pre>';
	print_r($summData);
	echo '</pre>';


?>




</body>
</html>