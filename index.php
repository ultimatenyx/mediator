<?php 
require_once 'class.Mediator.php';
$data = new Mediator();
if($_POST['content']){
	$processed = $data->parseContent($_POST['content']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $data->desc; ?></title>

<script type="text/javascript" src="assets/js/jquery.min.js"></script>
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<script src="assets/js/bootstrap.min.js"></script>


</head>
<body>
	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed"
					data-toggle="collapse" data-target="#bs-example-navbar-collapse-2">
					<span class="sr-only">Toggle navigation</span> <span
						class="icon-bar"></span> <span class="icon-bar"></span> <span
						class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#"><?php echo $data->title." v".$data->version; ?></a>
			</div>
		</div>
	</nav>

	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<h3><?php echo $data->desc; ?></h3>

				<form method="post" action="">
					<div class="form-group has-success">
						<label for="textArea" class="col-lg-2 control-label"><strong>Paste
								Projects Report Here --></strong></label>
						<div class="col-lg-10">
							<textarea name="content" id="content"
								class="form-control content" rows="15" cols="120"><?php echo @$_POST['content']; ?></textarea>
							<span class="help-block">Paste your text above this line.</span>
							<div class="checkbox">
							<?php 
							/**
							 * This is custom
							 */
							$checks = array(
									'1'=>'Name',
									'0'=>'Timestamp',
									'3'=>'Date'									
							);
							foreach ($checks as $val=>$eachcheck):
								
							?>
								<label> <input type="checkbox" name="show[]" value="<?php echo $val; ?>" <?php if(in_array($val,$_POST['show'])) echo "checked";?>> <?php echo $eachcheck; ?> </label> 
							<?php endforeach; ?>
							</div>
						</div>
					</div>

					<input type="submit" name="submit" value="submit"
						class="btn btn-default btn-lg">
				</form>
			</div>
		</div>
		<br />
		<div class="row">
			<div class="col-sm-12">
				<?php
// 				echo "<pre>";var_dump($processed);echo "</pre>";
				if(!empty($processed)){
					$userwise = array ();
					foreach ($processed as $eachentry){
						$userwise[$eachentry[4]][$eachentry[6]][strtotime($eachentry[0])]= array(
								'date'=>strtotime($eachentry[3]),
								'content'=>$eachentry[2],
								'notes'=>$eachentry[7],
								'rating'=>$eachentry[5]
						);
					}
				}
				
				
				foreach($userwise as $email=>$eachuser):
// 				var_dump(array_keys($userwise));
				$dummy = [];
				foreach ($eachuser as $timestamp=>$eachentry){
					$counter= 0;
					foreach ($eachentry as $eachdate=>$eachreport){
// 						echo $counter;
// 						echo "<pre>";print($eachreport["content"]);echo "</pre>";
						$contentsplit = explode(PHP_EOL,$eachreport["content"]);
						$last ="";
						foreach ($contentsplit as $eachline){
// 							$eachline = str_replace(array("\t","&tbsp;"," ","&#8203;")," ",$eachline);
							$eachline = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $eachline);
								
							if(strpos($eachline,"-")===0){
								$dummy[trim($eachline)]=[];
								$last = trim($eachline);
							}elseif(strpos($eachline,"-")>0){
								$dummy[$last][] = trim($eachline);
							}else{
								$dummy['otherpoints'][]=$eachline;
							}
							/* echo "<pre>";
							var_dump($eachline);
							var_dump(strpos($eachline,"-"));
							echo "</pre>"; */
						}
						/* if($counter>=3){
							die();
						} */
						/* echo "<pre>".$eachuser;
						var_dump($dummy);
						echo "</pre>"; */
// 						$counter++;
					}
					
				}
				echo "<pre>".$email;
				echo "<br />";
				echo "Count of updates = ".count($dummy);
				foreach ($dummy as $eachmajorupdate=>$minorUpdates){
					echo "<p>".$eachmajorupdate."=>".count($minorUpdates)."</p>";
				}
				echo "</pre>";
				
				endforeach;
// 				echo "<pre>";var_dump($userwise["nirmala.jadhav@inscripts.in"]);echo "</pre>";
				exit;
				if(!empty($processed)){
					$manipulated = array ();
					foreach ($processed as $eachentry){
						$manipulated[$eachentry[6]][$eachentry[1]." &lt;".$eachentry[4]."&gt;"][$eachentry[0]] = array(
								'date'=>$eachentry[3],
								'update'=>$eachentry[2],
								'notes'=>$eachentry[7],
								'rating'=>$eachentry[5]
						);
					}
					/**
					 * Printing logic
					 */
					foreach ($manipulated as $eachproject=>$userdata){
						?>
						<div class="panel panel-default">
							<?php 
							echo "<div class='panel-heading'>" . $eachproject . "</div><div class='panel-body'>";
							foreach ($userdata as $eachusername=>$eachreport){
								if(in_array("1",$_POST['show']))
									echo '<h5  class="text-danger">' . $eachusername . '</h5>';
								foreach ($eachreport as $eachdate=>$eachentry){
									if(in_array("0",$_POST['show']))
										echo '<strong><i>'.$eachdate."</i></strong><br />";
									if(in_array("3",$_POST['show']))
										echo '<p class="text-primary">'.$eachentry['date']."</p>";
									echo nl2br($eachentry['update'])."<br />";
									if($eachentry['notes']):
									?>
										<blockquote>
											<p><?php echo nl2br($eachentry['notes']); ?></p>
										</blockquote>
									<?php 
									endif;
								}
							}
							echo "</div>";
							?>
						</div>
						<?php 
					}
				}
				?>

			</div>
		</div>
	</div>
</body>
</html>
