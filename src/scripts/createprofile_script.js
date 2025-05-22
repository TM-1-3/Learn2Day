    const container = document.getElementById(userType === 'TUTOR' ? 'tutor-subjects-container' : 'student-subjects-container');
    
    const newEntry = document.createElement('div');
    newEntry.className = 'subject-entry';
    
    newEntry.innerHTML = `
            <?php if($user->type === 'TUTOR'): ?>
                <select name="subjects[]" class="subject-select">
                    <option value="">Select a subject</option>
                    <?php foreach (Qualifications::getAllSubjects() as $subject): ?>
                        <option value="<?= htmlspecialchars($subject) ?>"><?= htmlspecialchars($subject) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="tutor_level[]" class="grade-select">
                    <option value="">School level</option>
                    <?php foreach (Qualifications::getAllTutorLevels() as $tutor_level): ?>
                            <option value="<?= htmlspecialchars($tutor_level) ?>"><?= htmlspecialchars($tutor_level) ?></option>
                        <?php endforeach; ?>
                </select>
                <button type="button" class="remove-btn" onclick="removeSubject(this)">Remove</button>
            <?php else: ?>
                <select name="subjects[]" class="subject-select">
                    <option value="">Select a subject</option>
                    <?php foreach (Qualifications::getAllSubjects() as $subject): ?>
                        <option value="<?= htmlspecialchars($subject) ?>"><?= htmlspecialchars($subject) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="student_levels[]" class="grade-select">
                    <option value="">Grade level</option>
                    <?php foreach (Qualifications::getAllStudentLevels() as $student_level): ?>
                        <option value="<?= htmlspecialchars($student_level) ?>"><?= htmlspecialchars($student_level) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="remove-btn" onclick="removeSubject(this)">Remove</button>
            <?php endif; ?>
        `;
    
    container.appendChild(newEntry);


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
    newEntry.innerHTML = `
        <select name="languages[]" class="language-select">
            <option value="">Select a language</option>
            <?php foreach (Qualifications::getAllLanguages() as $language): ?>
                <option value="<?= htmlspecialchars($language) ?>"><?= htmlspecialchars($language) ?></option>
            <?php endforeach; ?>
        </select>
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