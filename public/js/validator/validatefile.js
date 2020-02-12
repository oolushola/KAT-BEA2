function validateCsv(ftype){
	var pixcount = 0;
var image_file = document.getElementById("file").value
var image_length = document.getElementById("file").value.length;
var pos = image_file.lastIndexOf('.') + 1;
var ext = image_file.substring(pos, image_length);
var final_ext = ext.toLowerCase();
var myftype = ftype;
var myftypes = myftype.split(",");
for (i = 0; i < myftypes.length; i++)
{
    if(myftypes[i] == final_ext)
    {
		pixcount = pixcount + 1;
	document.getElementById("filecheck").value = "1";	
    return true;
    }
}
alert('File with the following extentions '+myftype+" only allowed.");
document.getElementById("filecheck").value = "0";	
return false;
}
    


