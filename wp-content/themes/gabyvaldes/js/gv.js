function resizeCols()
{
	console.log("resizeCols()");
	var colL = $j(".reflexiones-col-left");
	var colR = $j(".reflexiones-col-right");
	var colLH = $j(".reflexiones-col-left").height();
	var colRH = $j(".reflexiones-col-right").height();

	if(colLH > colRH)
	{
		console.log("Left");
		colR.height(colLH);
	}
	else
	{
		console.log("Right");
		colL.height(colRH);
	}

	console.log(colLH + " R = " + colRH);

	$j(".edd_purchase_submit_wrapper").on( "click", columnResize );
		// $j( ".reflexiones-col-right" ).on( "click", columnResize );

	return;
}


function columnResize() 
{
  	var colR = $j(".reflexiones-col-right");
  	var colRH = $j(".reflexiones-col-right").height();
  	colR.height(colRH + 30);
  	resizeCols();
}
