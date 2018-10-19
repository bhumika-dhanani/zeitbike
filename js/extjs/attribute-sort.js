function addElementRight()
{
	var listEl = $('container1').select('option');
		var listEl2 = $('container2').select('option');
		var move = new Array();
		var count = 0;
		listEl.each(function(node){
			if($(node).selected == true)
			{
				$(node).selected = false;
				$('container2').insert($(node));
			}
		});
		var finalList=$('container2').innerHTML;
		$('catalogprint_general_product_attributes').update(finalList);
		selectFinalList();
		
}
function addElementLeft()
{
	var listEl = $('container2').select('option');
		var listEl2 = $('container1').select('option');
		var move = new Array();
		var count = 0;
		listEl.each(function(node){
			if($(node).selected == true)
			{
				$(node).selected = false;
				$('container1').insert(node);
			}
		});
		var finalList=$('container2').innerHTML;
		$('catalogprint_general_product_attributes').update(finalList);
		selectFinalList();
}
function addElementDown()
{
	var selectedop=document.getElementById('container2');
	for(i=selectedop.options.length-1;i >=0;i--)
	{
		if(selectedop.options[i].selected)
		{
			//alert(selectedop.options[i].value);
			if(i < selectedop.options.length-1 )
			{
				selectedop.options[i].selected=false;
				selectedop.options[i+1].selected=true;
				var tamp= selectedop.options[i+1].value;
				var text= selectedop.options[i+1].text;
				selectedop.options[i+1].value=selectedop.options[i].value;
				selectedop.options[i+1].text=selectedop.options[i].text;
				selectedop.options[i].value=tamp;
				selectedop.options[i].text=text;
			}
		}
	}
	var finalList=$('container2').innerHTML;
		$('catalogprint_general_product_attributes').update(finalList);
		selectFinalList();
}
function addElementUp()
{
	var selectedop=document.getElementById('container2');
	for(i=0;i <=selectedop.options.length-1;i++)
	{
		if(selectedop.options[i].selected)
		{
			if(i > 0 )
			{
				selectedop.options[i].selected=false;
				selectedop.options[i-1].selected=true;
				var tamp= selectedop.options[i-1].value;
				var text= selectedop.options[i-1].text;
				selectedop.options[i-1].value=selectedop.options[i].value;
				selectedop.options[i-1].text=selectedop.options[i].text;
				selectedop.options[i].value=tamp;
				selectedop.options[i].text=text;
			}
		}
	}
	var finalList=$('container2').innerHTML;
		$('catalogprint_general_product_attributes').update(finalList);
		selectFinalList();
}
function selectFinalList()
{
  var listEl = $('catalogprint_general_product_attributes').select('option');		
		listEl.each(function(node){			
				$(node).selected = true;							
		});		
}