<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 2/23/2023
 * Time: 9:23 AM
 */
?>

<form method="post" action="<?=$baseUrl?>/build">
    <textarea name="json" rows="25" cols="50"><?=$sample?></textarea>

    <br/>
    <button type="submit">Build</button>

</form>