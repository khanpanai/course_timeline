import * as config from 'core/config';
export const init = async() => {

    const courseSelect = document.querySelector("[name='ramctl-course-select']");

    await loadSections(courseSelect.value);
    await initSections(courseSelect.value);
    courseSelect.addEventListener('change', async function() {
        await loadSections(courseSelect.value);
        await initSections(courseSelect.value);
    });

};

const initSections = async (courseId) => {
    const sectionSelect = document.querySelector("[name='ramctl-section-select']");
    await loadTimeline(courseId, sectionSelect.value);
    await loadEndTime(courseId, sectionSelect.value);
    sectionSelect.addEventListener('change', async function() {
        await loadTimeline(courseId, this.value);
        await loadEndTime(courseId, sectionSelect.value);
    });
};

const loadSections = async(id) => {
    const startNode = window.document.querySelector("[data-section-select]");
    const requestUrl = new URL(`${config.wwwroot}/blocks/course_timeline/ajax.php?action=get_modules&courseId=${+id}`);
    const response = await fetch(requestUrl);
    const responseHtml = await response.text();
    window.console.log(responseHtml);
    startNode.textContent = "";
    startNode.insertAdjacentHTML("afterbegin", responseHtml);
};

const loadTimeline = async(courseId, id) => {
    const startNode = window.document.getElementById("ramctl-content");

    const requestUrl =
        new URL(`${config.wwwroot}/blocks/course_timeline/ajax.php?action=timeline&sectionId=${+id}&courseId=${+courseId}`);
    const response = await fetch(requestUrl);
    const responseHtml = await response.text();

    startNode.textContent = "";
    startNode.insertAdjacentHTML("afterbegin", responseHtml);

};

const loadEndTime = async(courseId, id) => {
    const startNode = window.document.querySelector("span[data-end]");

    const requestUrl =
        new URL(`${config.wwwroot}/blocks/course_timeline/ajax.php?action=end&sectionId=${+id}&courseId=${+courseId}`);
    const response = await fetch(requestUrl);
    const responseHtml = await response.text();

    startNode.innerHTML = responseHtml;
};