<?php
$pattern = '/3PHaNgomBkrvEL2QnuJarQVJa71wjw9qiqG_(?!total)([a-zA-Z0-9]+)_(share_tokens_(locked|blocked))/';
$url = 'http://nodes.wavesnodes.com/addresses/data/3P73HDkPqG15nLXevjCbmXtazHYTZbpPoPw';
$response = file_get_contents($url);
$data = json_decode($response, true);
$distribution = array();

foreach ($data as $item) {
    if (preg_match($pattern, ($item['key']), $match)) {
        $useraddr = $match[1];
        $amount = (int)$item['value'];
        if ($amount != 0) {
            if (array_key_exists($useraddr, $distribution)) {
                $distribution[$useraddr] += $amount;
            } else {
                $distribution[$useraddr] = $amount;
            }
        }
    }
}

$csv_file = fopen('distribution.csv', 'w');
fputcsv($csv_file, array('USERADDR', 'AMOUNT'));
foreach ($distribution as $useraddr => $amount) {
    fputcsv($csv_file, array($useraddr, $amount));
}
fclose($csv_file);

$json_data = array('data' => array());
foreach ($distribution as $useraddr => $amount) {
    $json_data['data'][] = array($useraddr => $amount);
}
$json_file = fopen('distribution.json', 'w');
fwrite($json_file, json_encode($json_data));
fclose($json_file);
?>
