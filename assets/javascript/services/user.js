export const deleteUser = async (userId) => {
    try {
        const url = `index.php?component=users&action=delete&id=${userId}`;

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const contentType = response.headers.get('content-type');

        if (contentType && contentType.includes('application/json')) {
            const result = await response.json();
            return result;
        } else {
            const text = await response.text();
            try {
                return JSON.parse(text);
            } catch {
                return { error: 'Réponse non-JSON du serveur: ' + text };
            }
        }
    } catch (error) {
        throw error;
    }
};

export const deleteAccount = async () => {
    const response = await fetch('index.php?component=user&action=delete-profile', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    return response;
};