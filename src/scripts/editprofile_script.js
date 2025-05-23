function getUserType() {
    const btn = document.querySelector('.add-btn');
    if (btn && btn.dataset.usertype) return btn.dataset.usertype;
    // fallback: try to infer from DOM
    if (document.querySelector('.T_signUp') || document.querySelector('.upload-btnT')) return 'TUTOR';
    return 'STUDENT';
}

function addSubject() {
    const container = document.getElementById('subjects-container');
    const userType = getUserType();
    const newEntry = document.createElement('div');
    newEntry.className = 'subject-entry';

    // Subject options
    let subjectOptions = '<option value="">Select a subject</option>';
    allSubjects.forEach(subject => {
        subjectOptions += `<option value="${subject}">${subject}</option>`;
    });

    // Grade/Level options based on user type
    let gradeOptions = '';
    if (userType === 'TUTOR') {
        gradeOptions = '<option value="">School level</option>';
        allTutorLevels.forEach(level => {
            gradeOptions += `<option value="${level}">${level}</option>`;
        });
    } else {
        gradeOptions = '<option value="">Grade level</option>';
        allStudentLevels.forEach(level => {
            gradeOptions += `<option value="${level}">${level}</option>`;
        });
    }

    if (userType === 'TUTOR') {
        newEntry.innerHTML = `
            <select name="subjects[]" class="subject-select">${subjectOptions}</select>
            <select name="tutor_levels[]" class="grade-select">${gradeOptions}</select>
            <button type="button" class="remove-btn" onclick="removeSubject(this)">Remove</button>
        `;
    } else {
        newEntry.innerHTML = `
            <select name="subjects[]" class="subject-select">${subjectOptions}</select>
            <select name="student_levels[]" class="grade-select">${gradeOptions}</select>
            <button type="button" class="remove-btn" onclick="removeSubject(this)">Remove</button>
        `;
    }
    container.appendChild(newEntry);
}

function removeSubject(button) {
    const entry = button.closest('.subject-entry');
    const container = document.getElementById('subjects-container');
    if (container.querySelectorAll('.subject-entry').length > 1) {
        entry.remove();
    } else {
        alert('You must have at least one subject');
    }
}

function addLanguage() {
    const container = document.getElementById('languages-container');
    const newEntry = document.createElement('div');
    newEntry.className = 'language-entry';

    let options = '<option value="">Select a language</option>';
    allLanguages.forEach(language => {
        options += `<option value="${language}">${language}</option>`;
    });

    newEntry.innerHTML = `
        <select name="languages[]" class="language-select">${options}</select>
        <button type="button" class="remove-btn" onclick="removeLanguage(this)">Remove</button>
    `;
    container.appendChild(newEntry);
}

function removeLanguage(button) {
    const entry = button.closest('.language-entry');
    const container = document.getElementById('languages-container');
    if (container.querySelectorAll('.language-entry').length > 1) {
        entry.remove();
    } else {
        alert('You must have at least one language');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const imagePreview = document.getElementById('image-preview');
    const uploadText = document.querySelector('.upload-text');
    const uploadIcon = document.querySelector('.upload-icon');
    if (imagePreview && imagePreview.src && !imagePreview.src.endsWith('/uploads/profiles/') && !imagePreview.src.endsWith('/uploads/profiles/default.png')) {
        imagePreview.style.display = 'block';
        if (uploadText) uploadText.style.display = 'none';
        if (uploadIcon) uploadIcon.style.display = 'none';
    }

    // Handle form submission
    const form = document.querySelector('form[action="edit_profile.php"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const userType = getUserType();
            const subjectEntries = document.querySelectorAll('#subjects-container .subject-entry');
            
            subjectEntries.forEach(entry => {
                const subjectSelect = entry.querySelector('select.subject-select');
                const gradeSelect = entry.querySelector('select.grade-select');
                
                if (subjectSelect && gradeSelect) {
                    // Combine subject and grade into the subject value
                    if (subjectSelect.value && gradeSelect.value) {
                        subjectSelect.value = subjectSelect.value + '|' + gradeSelect.value;
                    }
                }
            });
        });
    }
});