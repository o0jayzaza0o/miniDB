document.addEventListener("DOMContentLoaded", () => {
    loadMembers();
    
    document.getElementById("memberForm").addEventListener("submit", function (e) {
        e.preventDefault();
        const id = document.getElementById("memberId").value;
        if (id) {
            updateMember(id);
        } else {
            insertMember();
        }
    });
});

function showAlert(message, type = 'success') {
    const alert = document.getElementById('alert');
    alert.textContent = message;
    alert.className = `alert alert-${type}`;
    alert.style.display = 'block';
    setTimeout(() => {
        alert.style.display = 'none';
    }, 3000);
}

function loadMembers() {
    fetch("select_mem.php")
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            const memberList = document.getElementById("memberList");
            memberList.innerHTML = "";
            data.forEach(member => {
                memberList.innerHTML += `
                    <tr>
                        <td>${escapeHtml(member.id)}</td>
                        <td>${escapeHtml(member.name)}</td>
                        <td>${escapeHtml(member.email)}</td>
                        <td>${escapeHtml(member.phone)}</td>
                        <td>${escapeHtml(member.membership_type)}</td>
                        <td class="actions">
                            <button class="edit-btn" onclick="editMember(${member.id})">Edit</button>
                            <button class="delete-btn" onclick="confirmDelete(${member.id})">Delete</button>
                        </td>
                    </tr>
                `;
            });
        })
        .catch(error => {
            showAlert(error.message, 'error');
        });
}

function insertMember() {
    const form = document.getElementById("memberForm");
    const formData = new FormData(form);
    
    fetch("insert_mem.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.error) {
            alert(data.error);
        } else {
            form.reset();
            loadMembers();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the member');
    });
}

function editMember(id) {
    fetch(`fetch_mem.php?id=${encodeURIComponent(id)}`)
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            document.getElementById("memberId").value = data.id;
            document.getElementById("name").value = data.name;
            document.getElementById("email").value = data.email;
            document.getElementById("phone").value = data.phone;
            document.getElementById("membership_type").value = data.membership_type;
        })
        .catch(error => {
            showAlert(error.message, 'error');
        });
}

function updateMember(id) {
    const form = document.getElementById("memberForm");
    const formData = new FormData(form);
    formData.append("id", id);
    
    fetch("update_mem.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.error) {
            alert(data.error);
        } else {
            form.reset();
            document.getElementById("memberId").value = "";
            loadMembers();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the member');
    });
}

function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this member?')) {
        deleteMember(id);
    }
}

function deleteMember(id) {
    const formData = new FormData();
    formData.append('id', id);
    fetch("delete_mem.php", { 
        method: "POST", 
        body: formData 
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        showAlert('Member deleted successfully');
        loadMembers();
    })
    .catch(error => {
        showAlert(error.message, 'error');
    });
}

function escapeHtml(unsafe) {
    return unsafe
        .toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
