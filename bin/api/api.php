<?php
function callAPI($method, $url, $data = false) {
    $curl = curl_init();

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
//    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//    curl_setopt($curl, CURLOPT_USERPWD, "username:password");
	 $apiKey = $GLOBALS['apiKey'];
	 $headers = array(
			 "APIKEY: $apiKey" 
		 );

	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);
	// Capture cURL and HTTP info
	$curl_error = curl_error($curl);
	$http_code  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);

// Handle network-level errors
	if ($curl_error) {
		$_SESSION['api_error'] = "cURL Error: " . $curl_error;
	} else {
		$data = json_decode($result, true);
	 // ✅ Check if "errors" array exists in the response
		if (isset($data['errors']) && is_array($data['errors']) && count($data['errors']) > 0) {
			// Example: get the first error code
			$_SESSION['api_error'] = $data['errors'][0]['Code'] ?? 'UnknownError';
		} elseif ($http_code >= 400) {
			$_SESSION['api_error'] = "Billit API Error: " . $http_code;
		} else {
			// No errors, store success result
			$_SESSION['api_error'] = null;
		}
    return $result;
	}
}

function getClients($filter) {
	$endpoint = $GLOBALS['apiEndpoint'] . $GLOBALS['apiClients'] . "?$" . "filter=" . urlencode("PartyType eq 'Customer'");
	if($filter != '')
		$endpoint = $endpoint . '%20and%20' . urlencode($filter);
	
	$items = fetchAllODataItems($endpoint);
	$result = json_encode($items);

	$fileLocation = DATA_PATH . $_SESSION['wateringId'] . '_clients.json';
	file_put_contents($fileLocation, $result);
	return json_decode($result, true);
}

function getVendors($filter) {
	$endpoint = $GLOBALS['apiEndpoint'] . $GLOBALS['apiClients'] . "?$" . "filter=" . urlencode("PartyType eq 'Supplier'");
	if($filter != '')
		$endpoint = $endpoint . '%20and%20' . urlencode($filter);
	
	$items = fetchAllODataItems($endpoint);
	$result = json_encode($items);

	$fileLocation = DATA_PATH . $_SESSION['wateringId'] . '_vendors.json';
	file_put_contents($fileLocation, $result);
	
	return json_decode($result, true);
}

function getInvoicesIn($filter, $filterCosts, $wateringId) {
	$endpointInvoices = $GLOBALS['apiEndpoint'] . $GLOBALS['apiInvoices'] . "?$" . "filter=" . urlencode("OrderDirection eq 'Income'");
	if ($filter != '')
		$endpointInvoices .= '%20and%20' . urlencode($filter);

	$items = fetchAllODataItems($endpointInvoices);

	$endpointCosts = $GLOBALS['apiEndpoint'] . $GLOBALS['apiCosts'] . "?$" . "filter=" . urlencode("TransactionType eq 'Credit'");
	if ($filterCosts != '')
		$endpointCosts .= '%20and%20' . urlencode($filterCosts);

	$itemsCosts = fetchAllODataItems($endpointCosts);
	$itemsCostsNew = changeCosts($itemsCosts);

	foreach ($itemsCostsNew as $record) {
		$items[] = $record;
	}
	
	$itemsNew = changeInvoices($items, $wateringId);
	
	$result = json_encode($itemsNew);

	$fileLocation = DATA_PATH . $_SESSION['wateringId'] . '_invoicesIn.json';
	file_put_contents($fileLocation, $result);
	return json_decode($result, true);
}

function getInvoicesOut($filter, $filterCosts, $wateringId) {
	$endpointInvoices = $GLOBALS['apiEndpoint'] . $GLOBALS['apiInvoices'] . "?$" . "filter=" . urlencode("OrderDirection eq 'Cost'");
	if($filter != '')
		$endpoint = $endpoint . '%20and%20' . urlencode($filter);
	
	$items = fetchAllODataItems($endpointInvoices);

	$endpointCosts = $GLOBALS['apiEndpoint'] . $GLOBALS['apiCosts'] . "?$" . "filter=" . urlencode("TransactionType eq 'Debet'");
	if($filter != '')
		$endpointCosts = $endpointCosts . '%20and%20' . urlencode($filterCosts);
	
	$itemsCosts = fetchAllODataItems($endpointCosts);
	$itemsCostsNew = changeCosts($itemsCosts);

	foreach ($itemsCostsNew as $record) {
		$items[] = $record;
	}
	
	$itemsNew = changeInvoices($items, $wateringId);
	
	$result = json_encode($itemsNew);

	$fileLocation = DATA_PATH . $_SESSION['wateringId'] . '_invoicesOut.json';
	file_put_contents($fileLocation, $result);
	return json_decode($result, true);
}

function refreshBillit($wateringId) {
	$clients = getClients('');
	$vendors = getVendors('');
		
	refreshInvoices($wateringId);
	}
	
function refreshInvoices($wateringId) {
	$filterIn = '';
	$filterUit = '';
	$dateIn = new DateTime();
	$dateIn->sub(new DateInterval($_SESSION['invoice_filterIn']));
	$filterIn = "OrderDate ge datetime'" . $dateIn->format('Y-m-d\T00:00:00') . "'";	
	$filterCostIn = "ValueDate ge datetime'" . $dateIn->format('Y-m-d\T00:00:00') . "' and not AssignedEntities/any()";	

	$dateUit = new DateTime();
	$dateUit->sub(new DateInterval($_SESSION['invoice_filterUit']));
	$filterUit = "OrderDate ge datetime'" . $dateUit->format('Y-m-d\T00:00:00') . "'";
	$filterCostUit = "ValueDate ge datetime'" . $dateUit->format('Y-m-d\T00:00:00') . "' and not AssignedEntities/any()";
	
	$invoices = getInvoicesIn($filterIn, $filterCostIn, $wateringId);
	$invoices = getInvoicesOut($filterUit, $filterCostUit, $wateringId);
	}
	
function changeInvoices($items, $wateringId) {
	$facturen = getFacturenAll($wateringId, true);
	$facturenIDs = array_column($facturen, 'orderID');
	$notRelevant = getFacturenNotRelevant($wateringId, true);
	$notRelevantIDs = array_column($notRelevant, 'orderID');
	
    $map = [
        "Draft" => "Concept",
        "ToSend" => "Te verzenden",
        "ToPay" => "Te betalen",
        "ToInvoice" => "Te factureren",
        "ToDeliver" => "Te leveren",
        "ToDomiciliate" => "Te domiciliëren",
        "Sent" => "Verzonden",
        "Invoiced" => "Gefactureerd",
        "DeliveryNoteCreated" => "Leveringsbon aangemaakt",
        "OrderFormCreated" => "Bestelbon aangemaakt",
        "Credited" => "Gecrediteerd",
        "Refused" => "Geweigerd",
        "Canceled" => "Geannuleerd",
        "Paid" => "Betaald",
        "ApprovalNeeded" => "Goedkeuring vereist",
        "Delivered" => "Geleverd",
        "PaymentFileGenerated" => "Betaalbestand aangemaakt"
    ];

    foreach ($items as $key => $item) {
		$items[$key]['Invoiced'] = false;
		$items[$key]['NotRelevant'] = false;

		if (in_array($item['OrderID'], $notRelevantIDs)) {
			$items[$key]['Invoiced'] = true;
			$items[$key]['NotRelevant'] = true;
			}
		
		if (in_array($item['OrderID'], $facturenIDs)) {
			$items[$key]['Invoiced'] = true;
			$items[$key]['NotRelevant'] = false;
			}
			
        if (isset($map[$item['OrderStatus']])) {
            $items[$key]['OrderStatus'] = $map[$item['OrderStatus']];
        }
    }

    return $items;
	}

function changeCosts($items) {
    $out = [];

    foreach ($items as $cost) {

        // Build a new structure similar to invoice items
        $new = [];

        // Map fields
        $new['OrderID']    = $cost['BankAccountTransactionID'];
        $new['OrderDate']  = $cost['ValueDate'];
        // $new['PaidDate']   = $cost['ValueDate'];
        $new['TotalIncl']  = $cost['TotalAmount'];
		$new['IBAN']  = $cost['IBAN'];

        // Nested CounterParty structure
        $new['CounterParty'] = [
            'DisplayName' => $cost['NameCounterParty']
        ];

		$new['Reference'] = 'Geen Billitfactuur aanwezig';
		$new['OrderStatus'] = '';
		$new['OrderNumber'] = '';

        $out[] = $new;
    }

    return $out;
}

function fetchAllODataItems($baseEndpoint) {
    $allItems = [];
    $endpoint = $baseEndpoint;

    do {
        $result = callAPI('GET', $endpoint);
        $data = json_decode($result, true);

        if (!isset($data['Items'])) {
            break;
        }

        // Items toevoegen
        $allItems = array_merge($allItems, $data['Items']);

        // Bestaat er een volgende pagina?
        if (!empty($data['NextPageLink'])) {
            // Sommige APIs geven een volledige URL terug
            if (str_starts_with($data['NextPageLink'], 'http')) {
                $endpoint = $data['NextPageLink'];
            } else {
                $endpoint = $GLOBALS['apiEndpoint'] . $data['NextPageLink'];
            }
        } else {
            $endpoint = null;
        }

    } while ($endpoint !== null);

    return $allItems;
}
?>