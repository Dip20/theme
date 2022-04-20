<?php

if (!function_exists('url')) {

    function url($slug){
        return base_url() .'/'. $slug;
    }
}

if (!function_exists('html_convert')) {

    function html_convert($text){
        $text = str_replace("'", "", $text);
        $text = str_replace("\"", "", $text);

        return html_entity_decode($text);
    }
}

function date_compare($element1, $element2) {
    $datetime1 = strtotime($element1['date']);
    $datetime2 = strtotime($element2['date']);
    return $datetime1 - $datetime2;
} 

function generateRandomString($length = 5) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    $db = \Config\Database::connect();
    $builder = $db->table('property');
    $query = $builder->select('*')->where(array('pid' => $randomString))->get();
    $getdata = $query->getRow();
    if (!empty($getdata)) {
        $randomString = generateRandomString($length);
    }
    return $randomString;
}

function gl_list($abc,$test = array()){
        $db = \Config\Database::connect();
        $db->setDatabase(session('DataSource')); 
        $builder = $db->table('gl_group');
        $builder = $builder->select('GROUP_CONCAT(id) as ids');
        $builder->whereIn('parent',$abc);
        $query = $builder->get();
        $getglids = $query->getRow();

        $xyz = $test;
        if($getglids->ids != ''){
            
            $bijo =explode(',',$getglids->ids);
            $xyz = array_merge($xyz,$bijo);
            
            $xyz = gl_list($bijo,$xyz);
            
        }
        
        return $xyz;
}
function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    echo 'date : ';print_r($date);exit;

    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
    return $d && $d->format($format) === $date;
}

function db_date($date){
   
        if(!empty($date) && $date != '0000-00-00'){

            $dt = date_create($date);
            $year = $dt->format("Y");
            $ret_date = date_format($dt,'Y-m-d');

        }else{
            $ret_date = '';
        }
  
    return $ret_date;
}

function user_date($date){  
  
        if(!empty($date) && $date != '0000-00-00'){
            $dt = date_create($date);
            
            $ret_date = date_format($dt,'d-m-Y');
        }else{
            $ret_date = '';
        }   
   
    return $ret_date;
}

function to_time_ago( $time ) { 
    
	$diff = time() - $time; 
	if( $diff < 1 ) { 
		return 'less than 1 second ago'; 
	} 
	$time_rules = array ( 
		12 * 30 * 24 * 60 * 60 => 'year', 
		30 * 24 * 60 * 60	 => 'month', 
		24 * 60 * 60		 => 'day', 
		60 * 60				 => 'hour', 
		60					 => 'minute', 
		1					 => 'second'
	);
	foreach( $time_rules as $secs => $str ) { 	
		$div = $diff / $secs; 
		if( $div >= 1 ) { 		
			$t = round( $div ); 	
			return $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago'; 
		} 
	} 
} 

function getManagedData($tablename, $dt_col, $dt_search, $where, $dt_order = array()){ //print_r($aColumns);exit;
    $db = \Config\Database::connect();
    
    if(session('DataSource'))
    {
        $db->setDatabase(session('DataSource'));
    }
    $request = \Config\Services::request();
    $rResult = array();
    //$sQuery = "SELECT COUNT('*') AS row_count FROM " . $tablename;
    //$rResultTotal = $db->query($sQuery);
    // $aResultTotal = $rResultTotal->getRow();
    //$rResult[] = $aResultTotal->row_count;
    $post = $request->getPost();
    if (empty($post)) {
        $post = $request->getGet();
    }
    $draw = intval($post['draw']);
    $starts = $post['start'];
    $limit = $post['length'];

    $sLimit = "";
    $iDisplayStart = $post['start'];
    $iDisplayLength = $post['length'];
    
    if (isset($iDisplayStart) && $iDisplayLength != '-1') {
        $sLimit = "LIMIT " . intval($iDisplayStart) . ", " .
            intval($iDisplayLength);
    }

    $uri_string = urldecode($_SERVER['QUERY_STRING']);
    $uri_string = preg_replace("/%5B/", '[', $uri_string);
    $uri_string = preg_replace("/%5D/", ']', $uri_string);

    $get_param_array = explode("&", $uri_string);
    $arr = array();
    if (!empty($get_param_array)) {
        foreach ($get_param_array as $value) {
            $v = $value;
            $explode = explode("=", $v);
            $arr[$explode[0]] = $explode[1];
        }
    }

    $index_of_columns = $post["columns"];
    $index_of_start = $post["start"];


    /*
     * Ordering
     */
    $sOrder = "";
    for ($i = 0; $i < count($post['order']); $i++) {

        $sOrderIndex = $post['order'][$i]['column'];
        $sOrderDir = $post['order'][$i]['dir'];

        $bSortable_ = $post['columns'][$sOrderIndex]['orderable'];

        if ($bSortable_ == true) {
            if (empty($dt_order))
                $sOrder .= $dt_search[$sOrderIndex] . ($sOrderDir == 'asc' ? ' asc' : ' desc');
            else
                $sOrder .= $dt_order[$sOrderIndex] . ($sOrderDir == 'asc' ? ' asc' : ' desc');
        }
    }
    if ($sOrder != '')
        $sOrder = "ORDER BY " . $sOrder;

    if (!isset($post['order'][0]['column'])) {
        if (empty($dt_order))
            $sOrder .= $dt_search[0] . (' desc');
        else
            $sOrder .= $dt_order[0] . (' desc');
    }


    $sWhere = " WHERE 1 ";

    $sSearchVal = $post['search']['value'];
    if (isset($sSearchVal) && $sSearchVal != '') {

        $sWhere = $sWhere . "AND (";
        for ($i = 0; $i < count($dt_search); $i++) {
            $sWhere .= $dt_search[$i] . " LIKE '%" . str_replace('+', ' ', ($sSearchVal)) . "%' OR ";
        }
        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ')';
    }
    $sWhere .= $where;
    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $dt_col)) . "
			FROM $tablename
			$sWhere
			$sOrder
			$sLimit
			";
    //   echo $sQuery; exit;  
    $rResult[] = $db->query($sQuery);
    // echo "<pre>"; print_r($rResult->result_array());exit;
    // echo $db->getLastQuery(); exit;
    /* Data set length after filtering */
    $sQuery = "SELECT FOUND_ROWS() AS length_count";
    $rResultFilterTotal = $db->query($sQuery);
    $aResultFilterTotal = $rResultFilterTotal->getRow();
    $rResult[] = $aResultFilterTotal->length_count;
    //  print_r($rResult[1]->result_array()); exit;
    $result_return = array(
        'table' => $rResult[0]->getResultArray(), 'draw' => $draw,
        'total' => $rResult[1]
    );
    return $result_return;
    //$iFilteredTotal
}

function MakeThumb($source_path, $target_path, $width, $height, $defalusize = '600'){
    if ($height == $defalusize && $width == $defalusize) {
        $height = $defalusize;
        $width = $defalusize;
    } else if ($height >= $width && $height > $defalusize) {
        $calc = $height / $defalusize;
        $height = $defalusize;
        $width = $width / $calc;
    } else if ($height <= $width && $width > $defalusize) {
        $calc = $width / $defalusize;
        $width = $defalusize;
        $height = $height / $calc;
    } else {
        $width = $width;
        $height = $height;
    }


    $image = \CodeIgniter\Config\Services::image()
        ->withFile($source_path)
        ->resize(600, 600, true, 'height')
        ->save($target_path);
}

function uploadMultiFiles($fieldName, $uploadfolder){

    $year = date('Y');
    $month = date('m');
    $day = date('d');
    $original_path = "/" .$uploadfolder . "/" . $year . "/" . $month . "/" . $day . "/";

    if (!file_exists(getcwd() . $original_path)) {
        mkdir(getcwd() . $original_path, 0777, true);
    }
    
    $files = $_FILES;
    $randno = uniqid();
    $name = $files[$fieldName]['name'];
    $allowed = array('csv','xlsx','xls');
    $ext = pathinfo($name, PATHINFO_EXTENSION);

    if (!in_array($ext, $allowed)) {
        $response['errors'] = 'only CSV file Allowed';
        $response['is_success'] = 0;
    } else{

        $pathinfo = pathinfo($name);
        $imageName = $pathinfo['filename'] . '_' . $randno . "." . $pathinfo['extension'];
        $_FILES[$fieldName]['name'] = $imageName;
        $_FILES[$fieldName]['type'] = $files[$fieldName]['type'];
        $_FILES[$fieldName]['tmp_name'] = $files[$fieldName]['tmp_name'];
        $_FILES[$fieldName]['error'] = $files[$fieldName]['error'];

        $targetFile = getcwd() . $original_path . $imageName;
        $tempFile = $_FILES[$fieldName]['tmp_name'];
        if(move_uploaded_file($tempFile, $targetFile)){
            $response['fileName']  = $original_path . $imageName;
            $response['is_success'] = 1;
        } else {
            $response['errors'] = '';
            $response['is_success'] = 0;
        }
    }

    return $response;
}



    function prx($arr)
    {
      echo "<pre>";
      print_r($arr);
      echo "<pre>";
    }