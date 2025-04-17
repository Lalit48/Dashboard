// Global variables
let currentFilter = 'all';

// Search functionality
document.getElementById('searchWorkshop').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#workshopTableBody tr');
    
    rows.forEach(row => {
        const title = row.cells[0].textContent.toLowerCase();
        const status = row.dataset.status;
        const matchesSearch = title.includes(searchTerm);
        const matchesFilter = currentFilter === 'all' || status === currentFilter;
        
        row.style.display = matchesSearch && matchesFilter ? '' : 'none';
    });
});

// Filter functionality
function filterWorkshops(status) {
    currentFilter = status;
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    const rows = document.querySelectorAll('#workshopTableBody tr');
    rows.forEach(row => {
        if (status === 'all' || row.dataset.status === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Modal functions
function showAddWorkshopForm() {
    document.getElementById('modalTitle').textContent = 'Add New Workshop';
    document.getElementById('workshopId').value = '';
    document.getElementById('workshopForm').reset();
    document.getElementById('workshopModal').style.display = 'block';
}

function closeWorkshopModal() {
    document.getElementById('workshopModal').style.display = 'none';
}

function closeViewModal() {
    document.getElementById('viewWorkshopModal').style.display = 'none';
}

// Form submission
document.getElementById('workshopForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const workshopId = formData.get('workshop_id');
    const url = workshopId ? 'api/update_workshop.php' : 'api/add_workshop.php';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert(workshopId ? 'Workshop updated successfully' : 'Workshop added successfully');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Something went wrong'));
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
});

// Edit workshop
function editWorkshop(id) {
    fetch(`api/get_workshop.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
            document.getElementById('modalTitle').textContent = 'Edit Workshop';
            document.getElementById('workshopId').value = data.id;
            document.getElementById('title').value = data.title;
            document.getElementById('description').value = data.description;
            document.getElementById('workshopDate').value = data.workshop_date;
            document.getElementById('duration').value = data.duration;
            document.getElementById('capacity').value = data.capacity;
            document.getElementById('instructor').value = data.instructor;
            document.getElementById('status').value = data.status;
            document.getElementById('workshopModal').style.display = 'block';
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
}

// Delete workshop
function deleteWorkshop(id) {
    if(confirm('Are you sure you want to delete this workshop?')) {
        const formData = new FormData();
        formData.append('id', id);
        
        fetch('api/delete_workshop.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Workshop deleted successfully');
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Could not delete workshop'));
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    }
}

// View workshop details
function viewWorkshopDetails(id) {
    fetch(`api/get_workshop.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
            const detailsHtml = `
                <div class="detail-row">
                    <div class="detail-label">Title:</div>
                    <div class="detail-value">${data.title}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Description:</div>
                    <div class="detail-value">${data.description}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Date:</div>
                    <div class="detail-value">${data.workshop_date}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Duration:</div>
                    <div class="detail-value">${data.duration} hours</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Capacity:</div>
                    <div class="detail-value">${data.capacity}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Instructor:</div>
                    <div class="detail-value">${data.instructor}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value">
                        <span class="status-badge status-${data.status}">${data.status}</span>
                    </div>
                </div>
            `;
            document.getElementById('workshopDetails').innerHTML = detailsHtml;
            document.getElementById('viewWorkshopModal').style.display = 'block';
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
}

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}