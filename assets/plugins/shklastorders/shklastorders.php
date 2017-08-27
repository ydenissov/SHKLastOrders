<?php
if(!defined('MODX_BASE_PATH')){die('What are you doing? Get out of here!');}
$plugin_path = $modx->config['base_path'] . "assets/plugins/shklastorders/";
include($plugin_path.'lang/english.php');
if (file_exists($plugin_path.'lang/' . $modx->config['manager_language'] . '.php')) {
    include($plugin_path.'lang/' . $modx->config['manager_language'] . '.php');
}
$e = &$modx->Event;
if($e->name == 'OnManagerWelcomeHome'){
	$position = isset($position) ? $position : 0;
	$orders_quantity = isset($orders_quantity) ? $orders_quantity : 10;
	$module_name = isset($module_name) ? $module_name : "Shopkeeper";
	
	// Get id of module shopkeeper
	$res = $modx->db->select("id, name", $modx->getFullTableName('site_modules'),  "name = '$module_name'", '', '1');  
    if($modx->db->getRecordCount($res) >= 1) {
		$row = $modx->db->getRow($res);	
		$module_id = $row['id']; 
		// Get last orders
		$res = $modx->db->select("id, short_txt, price, currency, date, email, phone, payment, status, note, userid, content ", $modx->getFullTableName('manager_shopkeeper'),  " id != 0 ", 'id DESC', "$orders_quantity");  
		if($modx->db->getRecordCount($res) >= 1) {
			$data .= '
						<script>
							function shkChangeStatus(id, value){
								$.ajax({
									  method: \'post\',
									  url: \'index.php?a=112&id='.$module_id.'&action=status&item_id=\'+ id + \'&item_val=\'+ value + \'\',
									  success: function(){
										location.reload();
									  },
									  error: function(){
										alert(\''.$_lang['error_change'].'\');
									  }
									});
							  }
							  function shkDeleteItem(id){
								$.ajax({
									  method: \'post\',
									  url: \'index.php?a=112&id='.$module_id.'&action=delete\',
									  data: { item_id: id },
									  success: function(){
										location.reload();
									  },
									  error: function(){
										alert(\''.$_lang['error_delete'].'\');
									  }
									});
							  }
						</script>
						<thead>
							<tr>
								<th style="width: 1%">ID</th>
								<th>'.$_lang['client'].'</th>
								<th style="width: 15%">'.$_lang['date'].'</th>
								<th style="width: 1%">'.$_lang['price'].'</th>
								<th style="width: 17%">'.$_lang['status'].'</th>
								<th>'.$_lang['payment'].'</th>
								<th>'.$_lang['phone'].'</th>
								<th style="width: 1%; text-align: center">'.$_lang['actions'].':</th>
							</tr>
						</thead>
						<tbody>';  
			while( $row = $modx->db->getRow( $res ) ) {  
				$selected_1 = "";
				$selected_2 = "";
				$selected_3 = "";
				$selected_4 = "";
				$selected_5 = "";
				$selected_6 = "";
				$contacts = unserialize($row['short_txt']);
				switch ($row['status']) {
					case 1:
						$status_text = $_lang['status_1'];
						$selected_1 = "selected";
						break;
					case 2:
						$status_text = $_lang['status_2'];
						$selected_2 = "selected";
						break;
					case 3:
						$status_text = $_lang['status_3'];
						$selected_3 = "selected";
						break;
					case 4:
						$status_text = $_lang['status_4'];
						$selected_4 = "selected";
						break;
					case 5:
						$status_text = $_lang['status_5'];
						$selected_5 = "selected";
						break;
					case 6:
						$status_text = $_lang['status_6'];
						$selected_6 = "selected";
						break;
				}
				$content = unserialize($row['content']);
				
				$data .= '<tr>
								<td data-toggle="collapse" data-target=".collapse'.$row['id'].'"><span class="label label-info">'.$row['id'].'</span></td>
								<td data-toggle="collapse" data-target=".collapse'.$row['id'].'">'.$contacts['name'].'</td>
								<td data-toggle="collapse" data-target=".collapse'.$row['id'].'">'.$row['date'].'</td>
								<td data-toggle="collapse" data-target=".collapse'.$row['id'].'">'.$row['price'].'&nbsp;'.$row['currency'].'</td>
								<td>
									<select onchange="if(confirm(\''.$_lang['sure'].'\')){shkChangeStatus('.$row['id'].',this.value);};">
										<option value="1" '.$selected_1.'>'.$_lang['status_1'].'</option>
										<option value="2" '.$selected_2.'>'.$_lang['status_2'].'</option>
										<option value="3" '.$selected_3.'>'.$_lang['status_3'].'</option>
										<option value="4" '.$selected_4.'>'.$_lang['status_4'].'</option>
										<option value="5" '.$selected_5.'>'.$_lang['status_5'].'</option>
										<option value="6" '.$selected_6.'>'.$_lang['status_6'].'</option>
									</select>
								'.$status.'
								</td>
								<td data-toggle="collapse" data-target=".collapse'.$row['id'].'">'.$row['payment'].'</td>
								<td data-toggle="collapse" data-target=".collapse'.$row['id'].'">'.$row['phone'].'</td>
								<td style="text-align: right;" class="actions"><a title="'.$_lang['see'].'" href="index.php?a=112&id='.$module_id.'&action=show_descr&item_id='.$row['id'].'"><i class="fa fa-eye fa-fw"></i></a> <a title="'.$_lang['delete'].'" style="cursor:pointer" onclick="if(confirm(\''.$_lang['sure'].'\')){shkDeleteItem('.$row['id'].');}return false;"><i class="fa fa-trash fa-fw"></i></a></td>
							</tr>
							
							<tr class="resource-overview-accordian collapse collapse'.$row['id'].'">
								<td colspan="4">
									<div class="overview-body text-small">
									<p><strong>'.$_lang['contacts'].':</strong></p>
										<ul>
											<li><b>'.$_lang['client'].'</b>: '.$contacts['name'].'</li>
											<li><b>'.$_lang['email'].'</b>: '.$contacts['email'].'</li>
											<li><b>'.$_lang['phone'].'</b>: '.$contacts['phone'].'</li>
											<li><b>'.$_lang['shipping'].'</b>: '.$contacts['delivery'].'</li>
											<li><b>'.$_lang['payment'].'</b>: '.$contacts['payment'].'</li>
											<li><b>'.$_lang['comment'].'</b>: '.$contacts['message'].'</li>
											<li><b>'.$_lang['note'].'</b>: '.$row['note'].'</li>
										</ul>
									</div>
								</td>
								<td colspan="4">
									<div class="overview-body text-small">
									<p><strong>'.$_lang['order'].':</strong></p>
										<ul>';
					
				for ($i = 0; $i < count($content); $i++) {
					$url = $modx->makeUrl((int)$content[$i][0]);
					$data .= '<li><b><a href="'.$url.'" target="_blank">'.$content[$i][3].'</a></b> ('.$content[$i][1].') * '.$content[$i][2].' '.$row['currency'].'</li>';
				}
				
				
											
					$data .= '</ul>
									</div>
								</td>
							</tr>';  
				
			}  
			$data .= '</tbody>';  
		}
		else {
			$data = '<div class="card-body">'.$_lang['empty_orders'].'</div>';
		}
	}
	else {
		$data = '<div class="card-body">'.$_lang['plugin_not_found'].'</div>';
	}
	
	
	
	$widgets['shklastorders_widget'] = array(
        'menuindex' =>$position,
        'id' => 'shklastorders_widget',
        'cols' => 'col-sm-12',
        'icon' => 'fa-money',
        'title' => $_lang['plugin_name'],
        'body' => '
            <div class="widget-stage">
				<div class="table-responsive">
					<table class="table data">
						'.$data.'
					</table>
				</div>
			</div>
         ');
    $e->output(serialize($widgets));
}
