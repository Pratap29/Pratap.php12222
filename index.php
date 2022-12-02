<html>
   <body>
      
      <?php
         $note=<<<XML
         
         <note>
            <to>Gopal K Verma</to>
            <from>Sairamkrishna</from>
            <heading>Project submission</heading>
            <body>Please see clearly </body>
         </note>
         
         XML;
         $xml=simplexml_load_string($note);
         print_r($xml);
      ?>
		
   </body>
</html>
