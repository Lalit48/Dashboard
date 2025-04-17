// Global variables
let currentFilter = 'all';

// Search functionality
document.getElementById('searchBatch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#batchTableBody tr');
    
    rows.forEach(row => {
        const batchName = row.cells[0].textContent.toLowerCase();
        const status = row.dataset.status;
        const matchesSearch = batchName.includes(searchTerm);
        const matchesFilter = currentFilter === 'all' || status === currentFilter;
        
        row.style.display = matchesSearch && matchesFilter ? '' : 'none';
    });
});

// Filter functionality
function filterBatches(status) {
    currentFilter = status;
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    const rows = document.querySelectorAll('#batchTableBody tr');
    rows.forEach(row => {
        if (status === 'all' || row.dataset.status === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Modal functions
function showAddBatchForm() {
    document.getElementById('modalTitle').textContent = 'Add New Batch';
    document.getElementById('batchId').value = '';
    document.getElementById('batchForm').reset();
    document.getElementById('batchModal').style.display = 'block';
}

function closeBatchModal() {
    document.getElementById('batchModal').style.display = 'none';
}

// Form submission
document.getElementById('batchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const batchId = formData.get('batch_id');
    const url = batchId ? 'api/update_batch.php' : 'api/add_batch.php';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert(batchId ? 'Batch updated successfully' : 'Batch added successfully');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Something went wrong'));
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
});

// Edit batch
function editBatch(id) {
    fetch(`api/get_batch.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
            document.getElementById('modalTitle').textContent = 'Edit Batch';
            document.getElementById('batchId').value = data.id;
            document.getElementById('batchName').value = data.batch_name;
            document.getElementById('startDate').value = data.start_date;
            document.getElementById('endDate').value = data.end_date;
            document.getElementById('capacity').value = data.capacity;
            document.getElementById('status').value = data.status;
            document.getElementById('batchModal').style.display = 'block';
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
}

// Delete batch
function deleteBatch(id) {
    if(confirm('Are you sure you want to delete this batch?')) {
        const formData = new FormData();
        formData.append('id', id);
        
        fetch('api/delete_batch.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Batch deleted successfully');
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Could not delete batch'));
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    }
}

// View batch details
function viewBatchDetails(id) {
    fetch(`api/get_batch.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
            const detailsHtml = `
                <div class="detail-row">
                    <div class="detail-label">Batch Name:</div>
                    <div class="detail-value">${data.batch_name}</div>
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
                    <div class="detail-label">Capacity:</div>
                    <div class="detail-value">${data.capacity}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Status:</div>
                    <div class="detail-value">
                        <span class="status-badge status-${data.status}">${data.status}</span>
                    </div>
                </div>
            `;
            document.getElementById('batchDetails').innerHTML = detailsHtml;
            document.getElementById('viewBatchModal').style.display = 'block';
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