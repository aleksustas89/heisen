/**
 * Theme: Unikit - Responsive Bootstrap 5 Admin Dashboard
 * Author: Mannatthemes
 * Dropzone Js
 */
const handleChange = function(name) {

    const fileUploader = document.querySelector('#' + name);
    const getFile = fileUploader.files
    if (getFile.length !== 0) {
        const uploadedFile = getFile[0];
        readFile(uploadedFile, name);
    }
}

const readFile = function (uploadedFile, name) {
    if (uploadedFile) {
        const reader = new FileReader();
        reader.onload = function () {
            const parent = document.querySelector('#' + name + '-preview-box');
            parent.innerHTML = `<img class="preview-content" src=${reader.result} />`;
        };
        
        reader.readAsDataURL(uploadedFile);
    }
};

