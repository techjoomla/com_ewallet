
function checkforalpha(el)
{
	var i =0 ;
	for(i=0;i<el.value.length;i++){
		if((el.value.charCodeAt(i) > 64 && el.value.charCodeAt(i) < 92) || (el.value.charCodeAt(i) > 96 && el.value.charCodeAt(i) < 123)) 
		{
			alert('Please Enter Numerics'); 
			el.value = el.value.substring(0,i); 
			break;
		}
	}  
}


function selectstatusorder(appid,ele)
{
	var selInd=ele.selectedIndex;
	var status =ele.options[selInd].value;

	document.getElementById('hidid').value = appid;
	document.getElementById('hidstat').value = status;
	submitbutton('save');
   	return;
}


