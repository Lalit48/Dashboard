// Global variables
let currentFilter = 'all';

// Search functionality
document.getElementById('searchInternship').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#internshipTableBody tr');
    
    rows.forEach(row => {
        const title = row.cells[0].textContent.toLowerCase();
        const status = row.dataset.status;
        const matchesSearch = title.includes(searchTerm);
        const matchesFilter = currentFilter === 'all' || status === currentFilter;
        
        row.style.display = matchesSearch && matchesFilter ? '' : 'none';
    });
});

// Filter functionality
function filterInternships(status) {
    currentFilter = status;
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    const rows = document.querySelectorAll('#internshipTableBody tr');
    rows.forEach(row => {
        if (status === 'all' || row.dataset.status === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Modal functions
function showAddInternshipForm() {
    document.getElementById('modalTitle').textContent = 'Add New Internship';
    document.getElementById('internshipId').value = '';
    document.getElementById('internshipForm').reset();
    document.getElementById('internshipModal').style.display = 'block';
}

function closeInternshipModal() {
    document.getElementById('internshipModal').style.display = 'none';
}

function closeViewModal() {
    document.getElementById('viewInternshipModal').style.display = 'none';
}

// Form submission
document.getElementById('internshipForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const internshipId = formData.get('internship_id');
    const url = internshipId ? 'api/update_internship.php' : 'api/add_internship.php';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert(internshipId ? 'Internship updated successfully' : 'Internship added successfully');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Something went wrong'));
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
});

// Edit internship
function editInternship(id) {
    fetch(`api/get_internship.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
            document.getElementById('modalTitle').textContent = 'Edit Internship';
            document.getElementById('internshipId').value = data.id;
            document.getElementById('title').value = data.title;
            document.getElementById('description').value = data.description;
            document.getElementById('startDate').value = data.start_date;
            document.getElementById('endDate').value = data.end_date;
            document.getElementById('duration').value = data.duration;
            document.getElementById('stipend').value = data.stipend;
            document.getElementById('location').value = data.location;
            document.getElementById('requirements').value = data.requirements;
            document.getElementById('status').value = data.status;
            document.getElementById('internshipModal').style.display = 'block';
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
}

// Delete internship
function deleteInternship(id) {
    if(confirm('Are you sure you want to delete this internship?')) {
        const formData = new FormData();
        formData.append('id', id);
        
        fetch('api/delete_internship.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Internship deleted successfully');
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Could not delete internship'));
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    }
}

// View internship details
function viewInternshipDetails(id) {
    fetch(`api/get_internship.php?id=${id}`)
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
                    <div class="detail-label">Duration:</div>
                    <div class="detail-value">${data.duration} months</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Start Date:</div>
                    <div class="detail-value">${data.start_date}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">End Date:</div>
                    <div class="detail-value">${data.end_date}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Stipend:</div>
                    <div class="detail-value">₹${data.stipend}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Location:</div>
                    <div class="detail-value">${data.location}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Requirements:</div>
                    <div class="detail-value">${data.requirements}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value">
                        <span class="status-badge status-${data.status}">${data.status}</span>
                    </div>
                </div>
            `;
            document.getElementById('internshipDetails').innerHTML = detailsHtml;
            document.getElementById('viewInternshipModal').style.display = 'block';
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