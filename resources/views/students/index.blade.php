<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Info Management</title>
    <!-- Include Bootstrap for basic layout and responsiveness -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <!-- Link to the separate CSS file -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    <div class="container">
        <h1 class="text-center">Student Info Management</h1>
        
        <!-- Form Card for Adding/Editing Students -->
        <div class="card">
            <form id="studentForm">
                <input type="hidden" id="student_id">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" class="form-control" placeholder="Enter student name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" class="form-control" placeholder="Enter student email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" class="form-control" placeholder="Enter student phone" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Submit</button>
            </form>
        </div>

        <!-- Table to display the student list -->
        <table class="table table-striped mt-5">
            <thead class="thead-dark">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="studentTableBody">
                <!-- Student data will be appended here via JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Include the necessary JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', fetchStudents);

        async function fetchStudents() {
            const response = await fetch('/fetch-students');
            const students = await response.json();
            let studentTable = '';
            students.forEach(student => {
                studentTable += `
                    <tr>
                        <td>${student.name}</td>
                        <td>${student.email}</td>
                        <td>${student.phone}</td>
                        <td>
                            <button onclick="editStudent(${student.id})" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button onclick="deleteStudent(${student.id})" class="btn btn-danger">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </td>

                    </tr>
                `;
            });
            document.getElementById('studentTableBody').innerHTML = studentTable;
        }

        document.getElementById('studentForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const studentId = document.getElementById('student_id').value;
            const studentData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value
            };

            if (studentId) {
                await fetch(`/students/${studentId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(studentData)
                });
                alert('Student updated successfully');
            } else {
                await fetch('/students', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(studentData)
                });
                alert('Student added successfully');
            }
            fetchStudents();
            document.getElementById('studentForm').reset();
        });

        async function editStudent(id) {
            const response = await fetch(`/fetch-students`);
            const students = await response.json();
            const student = students.find(s => s.id == id);
            document.getElementById('name').value = student.name;
            document.getElementById('email').value = student.email;
            document.getElementById('phone').value = student.phone;
            document.getElementById('student_id').value = student.id;
        }

        async function deleteStudent(id) {
            if (confirm('Are you sure you want to delete this student?')) {
                await fetch(`/students/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                alert('Student deleted successfully');
                fetchStudents();
            }
        }
    </script>
</body>
</html>
