import * as config from 'core/config';

const courseCacheKey = "course_timeline_course_id";
const sectionCacheKey = "course_timeline_section_id";

export const init = async() => {

    const courseSelect = document.querySelector("[name='ramctl-course-select']");

    const cachedId = window.localStorage.getItem(courseCacheKey);

    if (cachedId) {
        const selectOptions = courseSelect.getElementsByTagName('option');
        for (const option of selectOptions) {
            if (option.value === cachedId) {
                option.selected = true;
            }
        }
    }
    await loadSections(cachedId ?? courseSelect.value);
    await initSections(cachedId ?? courseSelect.value);
    courseSelect.addEventListener('change', async function() {
        window.localStorage.setItem(courseCacheKey, courseSelect.value);
        await loadSections(courseSelect.value);
        await initSections(courseSelect.value);
    });

};

const initSections = async(courseId) => {
    const sectionSelect = document.querySelector("[name='ramctl-section-select']");

    const cachedId = window.localStorage.getItem(sectionCacheKey);

    if (cachedId) {
        const selectOptions = sectionSelect.getElementsByTagName('option');
        for (const option of selectOptions) {
            if (option.value === cachedId) {
                option.selected = true;
            }
        }
    }

    await loadTimeline(courseId, cachedId ?? sectionSelect.value);
    await loadEndTime(courseId, cachedId ?? sectionSelect.value);
    sectionSelect.addEventListener('change', async function() {
        window.localStorage.setItem("course_timeline_section_id", this.value);
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