function getUserType() {
    const btn = document.querySelector('.add-btn');
    if (btn && btn.dataset.usertype) return btn.dataset.usertype;
    if (document.querySelector('.T_signUp') || document.querySelector('.upload-btnT')) return 'TUTOR';
    return 'STUDENT';
}

function addSubject() {
    const userType = window.userType || (typeof getUserType === 'function' ? getUserType() : 'STUDENT');
    const container = document.getElementById(userType === 'TUTOR' ? 'tutor-subjects-container' : 'student-subjects-container');
    const newEntry = document.createElement('div');
    newEntry.className = 'subject-entry';

    let subjectOptions = '<option value="">Select a subject</option>';
    allSubjects.forEach(subject => {
        subjectOptions += `<option value="${subject}">${subject}</option>`;
    });

    let gradeOptions = '';
    if (userType === 'TUTOR') {
        gradeOptions = '<option value="">School level</option>';
        allTutorLevels.forEach(level => {
            gradeOptions += `<option value="${level}">${level}</option>`;
        });
        newEntry.innerHTML = `
            <select name="subjects[]" class="subject-select">${subjectOptions}</select>
            <select name="tutor_level[]" class="grade-select">${gradeOptions}</select>
            <button type="button" class="remove-btn" onclick="removeSubject(this)">Remove</button>
        `;
    } else {
        gradeOptions = '<option value="">Grade level</option>';
        allStudentLevels.forEach(level => {
            gradeOptions += `<option value="${level}">${level}</option>`;
        });
        newEntry.innerHTML = `
            <select name="subjects[]" class="subject-select">${subjectOptions}</select>
            <select name="student_levels[]" class="grade-select">${gradeOptions}</select>
            <button type="button" class="remove-btn" onclick="removeSubject(this)">Remove</button>
        `;
    }
    container.appendChild(newEntry);
}

function removeSubject(button) {
    const container = button.closest('#tutor-subjects-container, #student-subjects-container');
    if (container.children.length > 1) {
        button.closest('.subject-entry').remove();
    } else {
        alert('You need at least one subject');
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
    const container = document.getElementById('languages-container');
    if (container.children.length > 1) {
        button.parentElement.remove();
    } else {
        alert('You need at least one language');
    }
}