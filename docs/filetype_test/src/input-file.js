'use strict';

function upload_promise( up )
{
	var p1 = new Promise(function(resolve, reject){
		var reader = new FileReader;
		reader.onload = function(){
			//var hex = dec.toString(16);
			//var dec = parseInt(hex, 16);
			var hex = reader.result.byteLength.toString(16);
			var li = document.createElement('li');

			li.innerHTML = up.type + ' , ' + hex + ' , ' + up.name;

			PLIST.appendChild(li);
			resolve(1);
		}
		reader.readAsArrayBuffer(up);
	});
	return p1;
}

document.getElementById('pfile').addEventListener('change', function(e){
	var elem = this;
	elem.disabled = true;

	var promises = [];
	for ( var up of this.files )
	{
		console.log(up.type, up.name);
		promises.push( upload_promise(up) );
	} // for ( var up of this.files )

	Promise.all(promises).then(function(resolve){
		elem.disabled = false;
		console.log('promise then', resolve);
	});
});
