if (document.getElementById('userRole').value !== 'ROOT') {
    document.getElementById('userOption').style.display = 'none';
    document.getElementById('groupOption').style.display = 'none';
}
if (document.getElementById('userRole').value !== 'USER' && 
        document.getElementById('userRole').value !== 'ADMIN') {
    document.getElementById('userImagesOption').style.display = 'none';
    document.getElementById('userGroupsImagesOption').style.display = 'none';
}
if (document.getElementById('userRole').value !== 'ADMIN') {
    document.getElementById('sendEmailOption').style.display = 'none';
}