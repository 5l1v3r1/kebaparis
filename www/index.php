<!DOCTYPE html>

<html>

  <?php include 'header.php'; ?>
  
  
  <body onload="JavaScript:mygmap.initialize();"> <!-- map magic   MUSS NOCH WOANDERS HIN!!! -->
  
    <div id="login">
    
      <?php include 'usr.php'; ?> 
      

      
    </div> <!-- end div #login -->
    
    <div id="search">
    	<table>
					<tr> 
						<td> Search: </td>
						<td> <input type="text" name="q" onkeyup="letstype(this.value, '""')" size="20" /> </td>
						<td> <a href=""> Go </a> </td>
					</tr>
			</table>


    </div> <!-- end div #search --> 


<!-- Arvet test 1 -->
<div id="main">

	<div id="container">

		      <ul class="menu">  
		          <li id="new" class="active"> New </li>  
		          <li id="browse"> Browse </li>  
		          <li id="ranking"> Ranking </li>
							<li id="moderator"> Moderator </li>
							<li id="usrcntrl"> Control </li>
							<li id="kebapowner"> Kebapowner </li> 
		      </ul>
	 
		      <span class="clear"></span>  

		      <div class="content new"> 
							<?php include 'tabs/new.php'; ?>
					</div>

					<div class="content browse">  
		         	<?php include 'tabs/browse.php'; ?>
		      </div>

		      <div class="content ranking"> 
	 						<?php include 'tabs/ranking.php'; ?>
					</div>

		      <div class="content moderator"> 
	 						<?php include 'tabs/moderator.php'; ?>
					</div>

		      <div class="content usrcntrl"> 
	 						<?php include 'tabs/usrcntrl.php'; ?>
					</div>

		      <div class="content kebapowner"> 
	 						<?php include 'tabs/kebapowner.php'; ?>
					</div>
	</div>  <!-- container -->
</div> <!-- /main -->


<!-- Arvet end test 1 -->

	<div id="promo">
		<table>
			<tr>
				<!-- https://www.facebook.com/brandpermissions/logos.php -->
				<td> <a href="https://www.facebook.com/pages/kebaparisch/157140087677024" target="_blank"> Facebonk </a> </td>
			</tr>
			<tr>
				<td> <a href="http://twitter.com/#!/kebaparisch" target="_blank"> Twitta </a> </td>
			</tr>
			<tr>
				<td> <a href="mailto:info@kebaparis.ch"> Mailah </a> </td>
			</tr>
		</table>
	</div>
	<!-- end #promo -->
	
	
    <!--
    <div id="drminfo">
  		<table>
			<tr>
				<td> More Informations about Kebeabstand </td>
				<td> Hoore </td>
			</tr>  		
  		</table>
  	 </div> -->


	 <?php include 'footer.php'; ?>
	 
  </body>

</html>
