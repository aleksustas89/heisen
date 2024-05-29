@php

function showTree($groups, $current, $tab = '')
{
    foreach ($groups as $group) {
        if ($group["group"]->parent_id == 0) {
            $tab = ''; 
        }
        $cur = $group["group"]->id == $current ? "selected" : "";
        echo "<option ". $cur ." value='". $group["group"]->id ."'>" . $tab . $group["group"]->name ."</option>";
        if (isset($group["group"]["children"]) && count($group["group"]["children"]) > 0) {
            $tab .= '&nbsp;&nbsp;';
            showTree($group["group"]["children"], $current, $tab);
        }
    }
}

showTree($groups, $current);

@endphp

