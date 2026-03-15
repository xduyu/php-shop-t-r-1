const editWindow = document.getElementById('edit_window_u');
const addWindow = document.querySelector('.add_product'); // Блок добавления
const openButtons = document.querySelectorAll('.open_e_window');

const ename = document.getElementById('ename');
const edescription = document.getElementById('edescription');
const eprice = document.getElementById('eprice');

openButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
        ename.value = btn.dataset.name;
        edescription.value = btn.dataset.desc;
        eprice.value = btn.dataset.price;
        editWindow.classList.add('active_ef');
        editWindow.classList.remove('edit_window_u');
        addWindow.classList.add('add_product_invisible');
        editWindow.scrollIntoView({ behavior: 'smooth' });
    });
});

document.querySelectorAll('.open_e_window').forEach(button => {
    button.addEventListener('click', function () {
        const editWindow = document.getElementById('edit_window_u');
        editWindow.classList.add('active_ef');
        document.getElementById('edit_id').value = this.dataset.id;
        document.getElementById('ename').value = this.dataset.name;
        document.getElementById('edescription').value = this.dataset.description;
        document.getElementById('eprice').value = this.dataset.price;
        document.getElementById('edit_old_image').value = this.dataset.image;
    });
});

document.getElementById('open_e_window').addEventListener('click', () => {
    document.getElementById('edit_window_u').classList.remove('active_ef');
});