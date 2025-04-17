// Global variables
let currentFilter = 'all';

// Tab switching functionality
function switchTab(tabId) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab content
    const selectedTab = document.getElementById(tabId);
    if (selectedTab) {
        selectedTab.classList.add('active');
    }
    
    // Add active class to clicked button
    const selectedBtn = document.querySelector(`.tab-btn[onclick*="${tabId}"]`);
    if (selectedBtn) {
        selectedBtn.classList.add('active');
    }
}

// Search functionality
document.getElementById('searchStudent').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.data-table tbody tr');
    
    rows.forEach(row => {
        const name = row.cells[0].textContent.toLowerCase();
        const email = row.cells[1].textContent.toLowerCase();
        row.style.display = name.includes(searchTerm) || email.includes(searchTerm) ? '' : 'none';
    });
});

// Filter functionality
function filterStudents(batchId) {
    currentFilter = batchId;
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    const rows = document.querySelectorAll('#studentTableBody tr');
    rows.forEach(row => {
        if (batchId === 'all' || row.dataset.batch === batchId.toString()) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Modal functions
function showAddStudentForm() {
    const modal = document.getElementById('studentModal');
    const form = document.getElementById('studentForm');
    const validationMessage = document.getElementById('validationMessage');
    if (modal && form) {
        document.getElementById('modalTitle').textContent = 'Add New Student';
        document.getElementById('studentId').value = '';
        form.reset();
        validationMessage.style.display = 'none';
        const batchSelect = document.getElementById('batchIds');
        const workshopSelect = document.getElementById('workshopIds');
        const internshipSelect = document.getElementById('internshipIds');
        if (batchSelect) batchSelect.selectedIndex = -1;
        if (workshopSelect) workshopSelect.selectedIndex = -1;
        if (internshipSelect) internshipSelect.selectedIndex = -1;
        modal.style.display = 'block';
    }
}

function closeStudentModal() {
    const modal = document.getElementById('studentModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function closeViewModal() {
    const modal = document.getElementById('viewStudentModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Handle form submission
document.getElementById('studentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const validationMessage = document.getElementById('validationMessage');
    
    // Validate program selection
    const programType = formData.get('program_type');
    const programId = formData.get('program_id');
    
    if (!programType || !programId) {
        validationMessage.textContent = 'Please select both program type and program';
        validationMessage.style.display = 'block';
        return;
    }
    
    // Remove validation message if it was showing
    validationMessage.style.display = 'none';
    
    // Send form data to server
    fetch('api/add_student.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Student added successfully!');
            closeStudentModal();
            location.reload(); // Refresh the page to show new student
        } else {
            validationMessage.textContent = data.error || 'Error adding student';
            validationMessage.style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        validationMessage.textContent = 'Error submitting form';
        validationMessage.style.display = 'block';
    });
});

// Edit student
function editStudent(id) {
    fetch(`api/get_student.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
            document.getElementById('modalTitle').textContent = 'Edit Student';
            document.getElementById('studentId').value = data.id;
            document.getElementById('name').value = data.name;
            document.getElementById('email').value = data.email;
            document.getElementById('phone').value = data.phone;
            
            // Set selected batches
            const batchSelect = document.getElementById('batchIds');
            const enrolledBatches = data.batches ? data.batches.map(b => b.id.toString()) : [];
            Array.from(batchSelect.options).forEach(option => {
                option.selected = enrolledBatches.includes(option.value);
            });
            
            // Set selected workshops
            const workshopSelect = document.getElementById('workshopIds');
            const enrolledWorkshops = data.workshops ? data.workshops.map(w => w.id.toString()) : [];
            Array.from(workshopSelect.options).forEach(option => {
                option.selected = enrolledWorkshops.includes(option.value);
            });
            
            // Set selected internships
            const internshipSelect = document.getElementById('internshipIds');
            const enrolledInternships = data.internships ? data.internships.map(i => i.id.toString()) : [];
            Array.from(internshipSelect.options).forEach(option => {
                option.selected = enrolledInternships.includes(option.value);
            });
            
            document.getElementById('studentModal').style.display = 'block';
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
}

// Delete student
function deleteStudent(id) {
    if(confirm('Are you sure you want to delete this student?')) {
        const formData = new FormData();
        formData.append('id', id);
        
        fetch('api/delete_student.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Student deleted successfully');
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Could not delete student'));
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    }
}

// View student details
function viewStudentDetails(id) {
    fetch(`api/get_student.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
            
            let batchesHtml = '';
            if (data.batches && data.batches.length > 0) {
                batchesHtml = '<div class="detail-row"><div class="detail-label">Batches:</div><div class="detail-value">' +
                    data.batches.map(b => b.batch_name).join(', ') + '</div></div>';
            }
            
            let workshopsHtml = '';
            if (data.workshops && data.workshops.length > 0) {
                workshopsHtml = '<div class="detail-row"><div class="detail-label">Workshops:</div><div class="detail-value">' +
                    data.workshops.map(w => w.title).join(', ') + '</div></div>';
            }
            
            let internshipsHtml = '';
            if (data.internships && data.internships.length > 0) {
                internshipsHtml = '<div class="detail-row"><div class="detail-label">Internships:</div><div class="detail-value">' +
                    data.internships.map(i => i.title).join(', ') + '</div></div>';
            }
            
            const detailsHtml = `
                <div class="detail-row">
                    <div class="detail-label">Name:</div>
                    <div class="detail-value">${data.name}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Email:</div>
                    <div class="detail-value">${data.email}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Phone:</div>
                    <div class="detail-value">${data.phone}</div>
                </div>
                ${batchesHtml}
                ${workshopsHtml}
                ${internshipsHtml}
            `;
            
            document.getElementById('studentDetails').innerHTML = detailsHtml;
            document.getElementById('viewStudentModal').style.display = 'block';
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

// Initialize event listeners when the document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchStudent');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.data-table tbody tr');
            
            rows.forEach(row => {
                const name = row.cells[0].textContent.toLowerCase();
                const email = row.cells[1].textContent.toLowerCase();
                row.style.display = name.includes(searchTerm) || email.includes(searchTerm) ? '' : 'none';
            });
        });
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
}); 