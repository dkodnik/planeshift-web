//Not covered under the planeshift liscense.  Which free Liscense it used is unknown.  But it was available free.

function isNotEmptyText(objField){
	var strTrim = objField.value.replace(/\s+/g, "");
	if (strTrim.length>0) return true;   //field is filled out
	else return false;					  //field is empty
}

//---------------------

function isString(objField, strFieldDisplayName, blnCheckEmpty){
	if (blnCheckEmpty){   //can't be empty
		if (!isNotEmptyText(objField)){   //but is empty
			alert('Please fill out the field "'+strFieldDisplayName+'"');
			objField.focus();
			return false;
		}
	} else {  //might be empty
		if (!isNotEmptyText(objField)) return true;  //and is empty
	}
	return true;
}

function isInteger(objField, strFieldDisplayName, blnCheckEmpty){
	var testString = /\D/;

	if (blnCheckEmpty){   //can't be empty
		if (!isNotEmptyText(objField)){   //but is empty
			alert('Please fill out the field "'+strFieldDisplayName+'"');
			objField.focus();
			return false;
		}
	} else {  //might be empty
		if (!isNotEmptyText(objField)) return true;  //and is empty
	}

	if (testString.test(objField.value)==false) {
		return true;
	} else {
		alert('The value in the field "'+strFieldDisplayName+'" must be integer number');
		objField.focus();
		return false;
	}
}

function isFloat(objField, strFieldDisplayName, blnCheckEmpty){
	if (blnCheckEmpty){   //can't be empty
		if (!isNotEmptyText(objField)){   //but is empty
			alert('Please fill out the field "'+strFieldDisplayName+'"');
			objField.focus();
			return false;
		}
	} else {  //might be empty
		if (!isNotEmptyText(objField)) return true;  //and is empty
	}

	if (parseFloat(objField.value) == objField.value) {
		return true;
	} else {
		alert('The value in the field "'+strFieldDisplayName+'" must be integer or decimal number');
		objField.focus();
		return false;
	}
}

function isDate(objField, strFieldDisplayName, blnCheckEmpty){
	if (blnCheckEmpty){   //can't be empty
		if (!isNotEmptyText(objField)){   //but is empty
			alert('Please fill out the field "'+strFieldDisplayName+'"');
			objField.focus();
			return false;
		}
	} else {  //might be empty
		if (!isNotEmptyText(objField)) return true;  //and is empty
	}

	if ((Date.parse(objField.value))||(Date.parse("1/"+objField.value))) {
		return true;
	} else {
		alert('The value in the field "'+strFieldDisplayName+'" must be in the date format');
		objField.focus();
		return false;
	}
}

function isSelected(objField, strFieldDisplayName, blnCheckEmpty){
	if (blnCheckEmpty) {
		if (objField.selectedIndex > 0){
			return true;
		} else {
			alert('Please make a selection in the list "'+strFieldDisplayName+'"');
			objField.focus();
			return false;
		}
	} else {
		return true;
	}
}

function isEmail(objField, strFieldDisplayName, blnCheckEmpty) {
    if (blnCheckEmpty){   //can't be empty
		if (!isNotEmptyText(objField)){   //but is empty
			alert('Please fill out the field "'+strFieldDisplayName+'"');
			objField.focus();
			return false;
		}
	} else {  //might be empty
		if (!isNotEmptyText(objField)) return true;  //and is empty
	}
    if (objField.value.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1) {
        return true;
    } else {
		alert('The value in the field "'+strFieldDisplayName+'" should have valid email format');
		objField.focus();
        return false;
    }
}


function isRadioChecked(objField, strFieldDisplayName){
	var arRadio = eval(objField);
	var blnOutput = false;
	for (i=0; i<arRadio.length; i++){
		blnOutput |= arRadio[i].checked;
	}
	if (!blnOutput) {
		alert('Please make your choice in "'+strFieldDisplayName+'"');
		arRadio[0].focus();
		return false;
	}
	return true;
}

function isCheckboxesChecked(objField, strFieldDisplayName, intMin, intMax){
	var arChekboxes = eval(objField);
	var strMinPostfix = (intMin == 1)?' box':' boxes';
	var strMaxPostfix = (intMax == 1)?' box':' boxes';
	var intCheckedCount = 0;
	for (i=0; i<arChekboxes.length; i++){
		if (arChekboxes[i].checked) intCheckedCount ++;
	}
	if (intCheckedCount < intMin) {
		alert('You have to check at least '+intMin+strMinPostfix+' in the "'+strFieldDisplayName+'" . You have selected '+intCheckedCount);
		arChekboxes[0].focus();
		return false;
	}
	if (intCheckedCount > intMax) {
		alert('You cannot check more than '+intMax+strMaxPostfix+' in the "'+strFieldDisplayName+'" . You have selected '+intCheckedCount);
		arChekboxes[0].focus();
		return false;
	}
	return true;
}

function isMultipleSelected(objField, strFieldDisplayName, intMin, intMax){
	var arControl = eval(objField);
	var strMinPostfix = (intMin == 1)?' option':' options';
	var strMaxPostfix = (intMax == 1)?' option':' options';
	var intCount = 0;
	for (i=1; i<arControl.length; i++){
		if (arControl[i].selected) intCount ++;
	}
	if (intCount < intMin) {
		alert('You have to check at least '+intMin+strMinPostfix+' in the "'+strFieldDisplayName+'" . Not just '+intCount);
		arControl[0].focus();
		return false;
	}
	if (intCount > intMax) {
		alert('You have to check not more than '+intMax+strMaxPostfix+' in the "'+strFieldDisplayName+'" . Not '+intCount);
		arControl[0].focus();
		return false;
	}
	return true;
}
  //-------------------------------------------
  //------- FORM VALIDATION FUNCTION ----------
  //-------------------------------------------

function validateFormOnSubmit(){
	var arFormElements = validateFormOnSubmit.arguments;
	if (arFormElements.length > 0) {
		var blnOutput = true;

		var formIndex = arFormElements[0];   //form's index, usually 0

		for (j=1; j<arFormElements.length; j++){
			var objFormElement = arFormElements[j];   //array of each element parameters

			var strDisplayName = objFormElement[0];	  //field's display name
			var strValidationType = objFormElement[1];	  //type of validation to call

			if (strValidationType == "CheckboxesChecked"){
				var objField = formElement(formIndex, objFormElement[2]);	  //reference to the field as an object
				if (eval(objField)[0].type == "checkbox"){
					var intMin = objFormElement[3];	  //min to check
					var intMax = objFormElement[4];   //max to check

					blnOutput &= eval("is"+strValidationType+"('"+
						objField+"', '"+strDisplayName+"', "+intMin+", "+intMax+")");
				}else if (eval(objField)[0].type == "radio"){
					blnOutput &= eval("isRadioChecked('"+
						objField+"', '"+strDisplayName+"', true)");
				}

			} else if (strValidationType == "RadioChecked"){
				var objField = formElement(formIndex, objFormElement[2]);	  //"doc.forms["+formIndex+"]."+objFormElement[2];  //reference to the field as an object
				if (eval(objField)[0].type== "radio"){
					blnOutput &= eval("is"+strValidationType+"('"+
						objField+"', '"+strDisplayName+"')");
				}else if (eval(objField)[0].type == "checkbox"){
					blnOutput &= eval("isCheckboxesChecked('"+
						objField+"', '"+strDisplayName+"', 1, 3)");
				}
			} else if (strValidationType == "MultipleSelected"){
				var objField = formElement(formIndex, objFormElement[2]);
				var intMin = objFormElement[3];	  //min to check
				var intMax = objFormElement[4];   //max to check

				blnOutput &= eval("is"+strValidationType+"('"+objField+"','"+strDisplayName+"',"+intMin+","+intMax+")");
			} else {
				var blnCheckEmpty = objFormElement[2];   //to check for empty
				var objField = formElement(formIndex, objFormElement[3]);	  //"doc.forms["+formIndex+"]."+objFormElement[3];  //reference to the field as an object

				blnOutput &= eval("is"+strValidationType+"("+
					objField+", '"+strDisplayName+"', "+blnCheckEmpty+")");
			}
			if (!blnOutput) return false;
		}
	}
	return true;
}
