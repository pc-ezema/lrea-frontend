<?php

/**
 * @author Olusiyan Olutayo
 * @copyright 2012
 */

if (!function_exists('paginate_b4_query')) { /* Check_for "paginate_b4_query" */
function paginate_b4_query($count_query, $result_limit, $whichpage) {
    
    // Setup link to DB
    global $database_config, $config;
    
	// How many adjacent pages should be shown on each side?
	$adjacents = 2;
	
	/* 
	   First get total number of rows in data table. 
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
    mysqli_select_db($config, $database_config);
    $query = $count_query;
	$query_total_pages = mysqli_query($config, $query) or die(mysqli_error($config));
	$total_pages = mysqli_num_rows($query_total_pages);
	
	/* Setup vars for query. */
	$targetpage = $whichpage; 	//your file name  (the name of this file)
    if ($_GET) {
        $param = "";
		
		// Remove GET variables that are used in url rewriting
		$remove_variables = array('id', 'name', 'part_1', 'part_2', 'part_3', 'part_4');
		foreach ($remove_variables as $ind_variable) {
			if (array_key_exists($ind_variable, $_GET)) unset($_GET[$ind_variable]);
		}
		
		// Handle page numbers and other variables passed via GET
        if (array_key_exists("page", $_GET)) {
			$da_page = $_GET['page'];
            unset($_GET['page']);
			
            foreach ($_GET as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $key_in => $value_in) {
						$param .= $key."%5B%5D=".$value_in."&";
					}
				} else
                $param .= $key."=".$value."&";
            }
            $param = substr($param, 0, strlen($param)-1);
        } else {
            foreach ($_GET as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $key_in => $value_in) {
						$param .= $key."%5B%5D=".$value_in."&";
					}
				} else
                $param .= $key."=".$value."&";
            }
            $param = substr($param, 0, strlen($param)-1);
        }
    }
    if (!$param) {
        $param = "hl=en";
    }
    $targetpage .= "?".$param;
    
	$limit = $result_limit; 					//how many items to show per page
	$page = $da_page;
	if($page) {
		$start = ($page - 1) * $limit; 			//first item to display on this page
	}
	else {
		$start = 0;								//if no page var is given, set start to 0
	}
    
    //Setup array to return values
    $pg = array();
    $pg['pgadjacents'] = $adjacents;
    $pg['targetpage'] = $targetpage;
    $pg['total_pages'] = $total_pages;
    $pg['pglimit'] = $limit;
    $pg['pgstart'] = $start;
    if($page) {
        $pg['page'] = $page;
	} else $pg['page'] = 0;
	
	$_GET['page'] = $page;
    
    //Return values
    return $pg;
    
}
} /* End_check for "paginate_b4_query" */

/* End of paginate_b4_query  */



if (!function_exists('paginate_limit')) { /* Check_for "paginate_limit" */
function paginate_limit($sql, $pgstart, $pglimit) {
    $sql .= " LIMIT $pgstart, $pglimit";
    return $sql;
}
} /* End_check for "paginate_limit" */

if (!function_exists('paginate_after_limit')) { /* Check_for "paginate_after_limit" */
function paginate_after_limit($page, $total_pages, $targetpage, $pgadjacents, $pglimit, $append='') {
	/* Setup page vars for display. */
	if ($page == 0) $page = 1;					//if no page var is given, default to 1.
	$prev = $page - 1;							//previous page is page - 1
	$next = $page + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$pglimit);	//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1
	
	/* 
		Now we apply our rules and draw the pagination object. 
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{	
		$pagination .= '<div class="pagination-wrapper text-center"><ul class="pagination justify-content-center">';
		//previous button
		if ($page > 1) {
			$pagination.= '<li class="page-item"><a class="page-link" href="'.$targetpage.'&page='.$prev.$append.'" page="'.$prev.'">Previous</a></li>';
		}
		else {
			$pagination.= '<li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>';	
		}
		
		//pages	
		if ($lastpage < 7 + ($pgadjacents * 2))	//not enough pages to bother breaking it up
		{	
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination.= '<li class="page-item active"><a class="page-link" href="#">'.$counter.'</a></li>';
				else
					$pagination.= '<li class="page-item"><a class="page-link" href="'.$targetpage.'&page='.$counter.$append.'" page="'.$counter.'">'.$counter.'</a></li>';					
			}
		}
		elseif($lastpage > 5 + ($pgadjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page < 1 + ($pgadjacents * 2))		
			{
				for ($counter = 1; $counter < 4 + ($pgadjacents * 2); $counter++)
				{
					if ($counter == $page)
						$pagination.= '<li class="page-item active"><a class="page-link" href="#">'.$counter.'</a></li>';
					else
						$pagination.= '<li class="page-item"><a class="page-link" href="'.$targetpage.'&page='.$counter.$append.'" page="'.$counter.'">'.$counter.'</a></li>';					
				}
				$pagination.= '<li class="page-item"><a class="page-link" href="#">...</a></li>';
				$pagination.= '<li class="page-item"><a class="page-link" href="'.$targetpage.'&page='.$lpm1.$append.'" page="'.$lpm1.'">'.$lpm1.'</a></li>';
				$pagination.= '<li class="page-item"><a class="page-link" href="'.$targetpage.'&page='.$lastpage.$append.'" page="'.$lastpage.'">'.$lastpage.'</a></li>';		
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($pgadjacents * 2) > $page && $page > ($pgadjacents * 2))
			{
				$pagination.= '<li class="page-item"><a class="page-link" href="'.$targetpage.'&page=1'.$append.'" page="1">1</a></li>';
				$pagination.= '<li class="page-item"><a class="page-link" href="'.$targetpage.'&page=2'.$append.'" page="2">2</a></li>';
				$pagination.= '<li class="page-item"><a class="page-link" href="#">...</a></li>';
				for ($counter = $page - $pgadjacents; $counter <= $page + $pgadjacents; $counter++)
				{
					if ($counter == $page)
						$pagination.= '<li class="page-item active"><a class="page-link" href="#">'.$counter.'</a></li>';
					else
						$pagination.= '<li class="page-item"><a class="page-link" href="'.$targetpage.'&page='.$counter.$append.'" page="'.$counter.'">'.$counter.'</a></li>';					
				}
				$pagination.= '<li class="page-item"><a class="page-link" href="#">...</a></li>';
				$pagination.= '<li class="page-item"><a class="page-link" href="'.$targetpage.'&page='.$lpm1.$append.'" page="'.$lpm1.'">'.$lpm1.'</a></li>';
				$pagination.= '<li class="page-item"><a class="page-link" href="'.$targetpage.'&page='.$lastpage.$append.'" page="'.$lastpage.'">'.$lastpage.'</a></li>';		
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= '<li class="page-item"><a class="page-link" href="'.$targetpage.'&page=1'.$append.'" page="1">1</a></li>';
				$pagination.= '<li class="page-item"><a class="page-link" href="'.$targetpage.'&page=2'.$append.'" page="2">2</a></li>';
				$pagination.= '<li class="page-item"><a class="page-link" href="#">...</a></li>';
				for ($counter = $lastpage - (2 + ($pgadjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= '<li class="page-item active"><a class="page-link" href="#">'.$counter.'</a></li>';
					else
						$pagination.= '<li class="page-item"><a class="page-link" href="'.$targetpage.'&page='.$counter.$append.'" page="'.$counter.'">'.$counter.'</a></li>';					
				}
			}
		}
		
		//next button
		if ($page < $counter - 1) 
			$pagination.= '<li class="page-item"><a class="page-link" href="'.$targetpage.'&page='.$next.$append.'" page="'.$next.'">Next</a></li>';
		else
			$pagination.= '<li class="page-item disabled"><a class="page-link" href="#">Next</a></li>';
		$pagination.= "</ul></div>\n";		
	}
    
    return $pagination;
    
}
} /* End_check for "paginate_after_limit" */

/* End of paginate_after_limit  */

if (!function_exists('paginate_record_count')) { /* Check_for "paginate_record_count" */
function paginate_record_count($pglimit, $total_pages, $page) {
	if ($page > 0) {
		$page = $page - 1;
	} else $page = 0;
	
	$pgfrom = ($page * $pglimit) + 1;
	$pgto = min(($page * $pglimit) + $pglimit, $total_pages);
	$pgof = $total_pages;
	
	return '<strong>'.$pgfrom.'</strong> to <strong>'.$pgto.'</strong> of <strong>'.$pgof.'</strong>';
}
} /* End_check for "paginate_record_count" */

?>