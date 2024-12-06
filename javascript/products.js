function showAddPopup() {
    document.getElementById('addProductPopup').style.display = 'flex';
}

function showEditPopup(product) {
    document.getElementById('editProductId').value = product.id;
    document.getElementById('editTitle').value = product.title;
    document.getElementById('editCategory').value = product.category;
    document.getElementById('editDescription').value = product.description;
    document.getElementById('editPrice').value = product.price;
    document.getElementById('editProductPopup').style.display = 'flex';
}


window.onclick = function(event) {
    if (event.target.classList.contains('popup')) {
        event.target.style.display = 'none';
    }
}