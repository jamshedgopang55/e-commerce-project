<?php
use App\Models\category;

function getCategories(){
    return category::orderBy('name','ASC')->with('subCategory')->where('status',1)->where('showOnHome','Yes')->get();
}
function  featuredProduct(){
    return product::where('is_featured','YES')->where('status',1)->get();
}

?>