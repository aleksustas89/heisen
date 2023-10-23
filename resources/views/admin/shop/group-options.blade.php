@php

function showTree($groups, $tab = '')
{
    foreach ($groups as $group) {
        if ($group["group"]->parent_id == 0) {
            $tab = ''; 
        }
        echo "<option value='". $group["group"]->id ."'>" . $tab . $group["group"]->name ."</option>";
        if (isset($group["group"]["children"]) && count($group["group"]["children"]) > 0) {
            $tab .= '&nbsp;&nbsp;';
            showTree($group["group"]["children"], $tab);
        }
    }
}

showTree($groups);

@endphp

