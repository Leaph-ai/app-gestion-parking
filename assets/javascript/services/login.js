export const login = async (email, password) => {
    console.log('Tentative de connexion avec:', { email, password });

    // Créer les données à envoyer
    const data = new URLSearchParams();
    data.append('email', email);
    data.append('password', password);

    try {
        const response = await fetch(`index.php?component=login`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: data.toString() // Ajout de .toString() important !
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        console.log('Résultat de connexion:', result);
        return result;
    } catch (error) {
        console.error('Erreur lors de la requête:', error);
        throw error;
    }
}