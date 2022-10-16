<?php
  $url = url('') ///capture hosting domain url
?>
<!DOCTYPE html>
<html>
  <head>
  </head>
  <body>
    <p>Verification QR Code for Certificate Number: <?php echo e($certificate->certificate_number); ?></p>
    <div class="visible-print text-center">
      <br>
      <?php echo QrCode::size(200)->generate($url.'?search='.$certificate->certificate_number); ?>

  </div>
  </body>
</html>
<?php /**PATH D:\xampp\htdocs\verify-cert\resources\views/qrcode.blade.php ENDPATH**/ ?>