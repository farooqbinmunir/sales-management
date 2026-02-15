<?php 
$options = $data['options'] ?? $data;
$row_to_skip = $data['row_to_skip'] ?? '';
?>
<div id="searchFilter">
    <div>Search by:</div>
    <select id="searchFilterType">
        <?php 
        foreach($options as $key => $value){
            echo "<option value='{$key}'>{$value}</option>";
        }
        ?>
    </select>
    <?php include_component('search-field', ['search_column' => key($options) ?? 'pname', 'row_to_skip' => $row_to_skip]); ?>
</div>