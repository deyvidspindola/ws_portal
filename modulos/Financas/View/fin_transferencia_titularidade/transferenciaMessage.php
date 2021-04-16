<?php //Transferência

$success = $data['success'];

$message = $data['message'];

if($success === 0){
?>
<div class="mensagem erro" id="msgTransferencia" > <?php echo $message; ?> </div>
<?php 

}else if($success === 1){
?>

<div class="mensagem alerta" id="msgTransferencia" >  <?php echo $message; ?>  </div>

<?php 

}else if($success === 2){
?>

<div class="mensagem alerta" id="msgTransferencia" >  <?php echo $message; ?>  </div>

<?php 
}
?>
