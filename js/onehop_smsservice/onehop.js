function getTemplate(){
    var templateid = document.getElementById("smstemplate").value;
    var currentLocation = window.location;
    document.getElementById("sms_text").value = '';
    if(templateid > 0) {
        document.getElementById("sms_text").value = 'Loading template body...';
        xmlhttp=new XMLHttpRequest();
        xmlhttp.onreadystatechange=function()
        {
            if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
                //document.getElementById("sms_text").innerHTML=xmlhttp.responseText;
                var templatebody = JSON.parse(xmlhttp.responseText);
                document.getElementById("sms_text").value=templatebody['temp_body'];
            }
        }
        xmlhttp.open("GET",currentLocation+"templateid/"+templateid,true);
        xmlhttp.send();
    }
}

function deleteTemplates(id, deleteUrl) {
    var isConf = confirm("Are you sure to delete this template?");
    if(isConf == true) {
        if(id > 0) {
            xmlhttp=new XMLHttpRequest();
            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4 && xmlhttp.status==200)
                {
                    var isadded = JSON.parse(xmlhttp.responseText);
                    
                    if(isadded['allready_assigned'] == 1) {
                        alert('You can not delete this template because it\'s already assigned in SMS Automation');
                    } else {
                        alert('Deleted successfully');
                        window.location.reload();
                    }
                }
            }
            xmlhttp.open("GET",deleteUrl+'smstemplateid/'+id,true);
            xmlhttp.send();
        }
    }
}

function editTemplates(id, editUrl) {
    window.location.href = editUrl+'smstemplateid/'+id;
}

function checked(id){
    var checkbox = document.getElementById ('checkbox-'+id);
    
    for(i=1; i<=3; i++){
        var chk = document.getElementById ('checkbox-'+i);
        if(id == i) chk.checked = checkbox.checked;
        else chk.checked = false;
        
        document.getElementById ('caret-'+i).className = "indicator fa fa-caret-right";
    }
    if(checkbox.checked)
        document.getElementById ('caret-'+id).className = "indicator fa fa-caret-right";
    else
        document.getElementById ('caret-'+id).className = "indicator fa fa-caret-down";
}