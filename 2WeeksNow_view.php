<form action="2WeeksNow.php" method="post">
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
<br />
<h1> Last 5 "2WeeksNow" Requests </h1>
<br /> 
<table cellspacing="0" cellpadding="0" border="0" width="100%"  class="zebraTable">
    <tbody>
        <tr>
            <tr>
                <th> Company</th>
                <th> Size </th>
                <th> Date </th>
                <th> Link </th>
            </tr>
        </tr>
        <?php
        if (sizeOf($currentUserRequests)) 
        foreach($currentUserRequests as $userRequest) {?>
        <tr class="<?php echo $key % 2 != 0 ? 'odd' : 'even' ?>">
            <td><?php echo $userRequest['country']['countryName']?></td>
            <td><?php echo $userRequest['industry']['industry']?></td>
            <td>
                
                <?php 
                    unset($userRequest['dealType']['id']);
                    if ($userRequest['dealType']['subtype2'] == 'n/a') unset($userRequest['dealType']['subtype2']);
                    echo join(' > ', $userRequest['dealType']);
                ?>
            </td>
            <td><a href="2WeeksNow.php?requestId=<?php echo $userRequest['id']?>" title="View request results"> View </a></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<script>
jQuery(function(){
	jQuery('#country').selectmenu();
	jQuery('#industry').selectmenu();
	jQuery('#dealType').selectmenu();
});
</script>