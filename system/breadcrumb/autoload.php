<?php 

function breadcrumb ($list, $links) {
	global $bread;
	
	$l = explode("|", $list);
	$u = explode("|", $links);
	
	$bread = '<ul class="breadcrumb">';
	
	$i = 0;
	
	foreach($l as $montado) {
		
		$i = $i;
		
		if($montado == end($l)) {
			$bread .= '<li class="active">'.$montado.'</li>';
		} else {
		if($l[0] == 'Home' and $l[0] == $montado) {
			$montado = '<i class="fas fa-home"></i>';
		}
		$bread .= '<li><a href="'.$u[$i].'">'.$montado.'</a><div class="divider">&rsaquo;</div></li>';
		
		} $i = $i+1;
	}
	
	$bread .= '<li class="pull-right" id="breadback"><a href="javascript:history.go(-1)">Voltar</a></li>
</ul><script>
if(history.length <= 1) {
	$("#breadback").hide();
}
</script>';

	return $bread;
  
}