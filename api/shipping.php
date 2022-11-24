<?php

require_once '../includes/utils.php';

header('Content-Type: application/json');
cors();

$result = [
  'success' => false,
  'message' => 'Invalid request',
  'fee' => 0
];

$brgy = !empty($_GET['brgy']) ? $_GET['brgy'] : null;
if ($brgy) {
  switch ($brgy) {
    case 'Ma-ao':
      $result['fee'] = 20;
      break;

    case 'Bacong-Montilla':
    case 'Binubuhan':
    case 'Don Jorge L. Araneta':
    case 'Ilijan':
    case 'Mailum':
      $result['fee'] = 30;
      break;

    case 'Alianza':
    case 'Atipuluan':
      $result['fee'] = 40;
      break;

    case 'Abuanan':
    case 'Caridad':
    case 'Pacol':
    case 'Sagasa':
      $result['fee'] = 45;
      break;

    case 'Bagroy':
    case 'Balingasag':
    case 'Busay':
    case 'Dulao':
    case 'Lag-Asan':
    case 'Malingin':
    case 'Napoles':
    case 'Poblacion':
      $result['fee'] = 50;

    case 'Calumangan':
    case 'Tabunan':
    case 'Taloc':
    case 'Sampinit':
      $result['fee'] = 55;
      break;
    
    default:
      $result['fee'] = 0;
  }

  if ($result['fee'] > 0) {
    $result['success'] = true;
    $result['message'] = '';
  } else {
    $result['message'] = 'Barangay was not found';
  }
}

echo json_encode($result);

?>
