<?php
  
?>
<form action="oneStop.php" method="post">
<table width="100%" border="0" cellspacing="3" cellpadding="3">
      <tr>
        <td align="center" width="24%"><strong>Meeting details:</strong></td>
        <td align="center"  width="24%">
        <select name="country" id="country">
        <option value="0" >Which Country</option>
        <?php 
           if (count($countries))
            foreach ($countries as $country): ?>
            <option value="<?php echo $country['id']?>"> <?php echo $country['name'] ?> </option>
        <?php endforeach ?>
        </select></td>
        <td align="center"  width="24%">
        <select name="industry" id="industry">
        <option value="0" >Which Industry</option> 
        <?php if (count($sortedIndustries)) 
        foreach ($sortedIndustries as $key=>$industries) {
            echo "<optgroup label='$key' >";
            if (count($industries)) {
                foreach  ($industries as $industry) {
                    $value = $industry['id'];
                    $label = $industry['industry'];
                    echo "<option value='$value'>$label</option>";
                }
                
            }
            echo "</optgroup>";
        }
        ?>
        </select></td>
        <td align="center"  width="24%">
        <select name="dealType" id="dealType">
            <option value="0" >Which Deal Type</option>
            <?php if (count($sortedCategories))
                foreach ($sortedCategories as $mainCategoryLabel=>$category)  {
                    echo "<optgroup label='$mainCategoryLabel' >"; 
                    if (count($category)) {
                        foreach ($category as $key=>$subCat) { 
                         
                            if (sizeOf($subCat)) {
                                   $val = $subCat['name']; 
                                   $id = $subCat['id']; 
                                   $class = $subCat['class'];
                                   $spaces = '';
                                   switch ($class) {
                                       case 'subtype':
                                        $spaces = str_repeat('&nbsp',2);
                                       break;
                                       case 'sub-subtype':
                                        $spaces = str_repeat('&nbsp',4);
                                       break;
                                   }
                                   echo "<option value='$id'>$spaces $val</option>"; 
                                }
                            }
                          
                        }
                    echo "</optgroup>"; 
                }
            ?>
        </select></td>
        <td align="center"  width="24%"><input type="submit" value="Results, please" class="btn_auto" name="submit"></td>
    </tr>
  </table>
</form>
<br />

<div style='color:#3B3B3B;font:11px/18px Tahoma,Geneva,sans-serif;text-align:left;vertical-align:top;' id="explanation">
The "One Stop" page is designed to give you all the data that you need in one place for an upcoming meeting.<br /> You give us 3 pieces of information: Country / Industry / Deal Type for your upcoming meeting and we give you: <br /><br />
<ul>
    <li> The relevant League Tables</li>
    <li> Credentials / Tombstones</li>
    <li> Volume charts</li>
    <li> Top 10 deals</li>
    <li> List of relevant transactions your top comepetitors have done recently</li>
    <li> List of relevant deals that your adjacent teams (debt, equity, M&A) have done, in case you wish to cross sell</li>
</ul>
<br />
Then you analyze the data and make adjustments. For example use out "Make Me Top" algorithm to help present your league table position in a more favourable light.
<br />
Finally you adjust the different searches, save what you need, and download it to Powerpoint.
</div>
<br />
<br />
<h1> Last 5 "One Stop" Requests </h1>
<br /> 
<table cellspacing="0" cellpadding="0" border="0" width="100%"  class="zebraTable">
    <tbody>
        <tr>
            <th width="19%">Country</th>
            <th width="19%">Industry</th>
            <th width="19%">Deal</th>
            <th width="19%">Date submitted</th>
            <th width="19%">View</th>
        </tr>
        <?php
        if (sizeOf($currentUserRequests)) 
        foreach($currentUserRequests as $key=>$userRequest) {?>
        <tr class="<?php echo $key % 2 != 0 ? 'odd' : 'even' ?>">
            <td><?php echo $userRequest['countryName']?></td>
            <td><?php echo $userRequest['industryName']?></td>
             
            <?php //echo "<div style='display:none'> <pre> ".print_r($userRequest,1). "</pre></div>";
                $dealType =  $userRequest['dealSubtype2'];
                if ($userRequest['dealSubtype2'] == '' || $userRequest['dealSubtype2'] == 'n/a') {
                    if ($userRequest['dealType'] == 'M&A') {
                        
                        if ($userRequest['dealSubtype1'] == 'All') {
                            $dealType = 'M&A Completed & Pending';
                        } else {
                            $dealType = 'M&A ' . $userRequest['dealSubtype1'];
                        }
                    } else {
                        $dealType = 'All ' . $userRequest['dealSubtype1'];
                    }
                }
            ?>
            <td><?php echo $dealType ?></td>
            <td><?php echo $userRequest['dateSubmitted']?></td>
            <td><a class="link_as_button" href="oneStop.php?action=viewRequest&requestID=<?php echo $userRequest['id']?>" title="View request results"> View </a></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<script>
$(function(){
	$('#country').selectmenu();
	$('#industry').selectmenu();
	$('#dealType').selectmenu();
});
</script>