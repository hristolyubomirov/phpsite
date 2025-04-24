export async function fetchSurveyScreens() {
    try {
        const response = await fetch('http://127.0.0.1:8000/survey', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error(`API Error: ${response.statusText}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Error fetching survey screens:', error);
        throw error;
    }
}
