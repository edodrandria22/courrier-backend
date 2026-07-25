# Pour creer une nouvelle utilisateur

**Url:** `POST /utilisateurs`

**Header:** `Content-Type: application/json`

**Authorization:** `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ5b3VyLWFwcCIsImF1ZCI6InlvdXItY2xpZW50IiwiaWF0IjoxNzczMjk4MTg2LjE2NjQ4OSwiZXhwIjoxNzczMzAxNzg2LjE2NjQ4OSwiZW1haWwiOiJhZG1pbkBnbWFpbC5jb20iLCJub20iOiJBZG1pbiIsInByZW5vbSI6IkFkbWluIiwiYWRyZXNzZSI6IkFua2F0c28gLCBwb3J0ZSAxMDQiLCJpZCI6MSwicm9sZSI6IkFkbWluIn0.qaMEC_5W3hgEU5fnavlRuzfZFViP22dZ-CPppZRvDjE`

**Role:**`Admin`

**Body:**

```json
{
  "email": "jean.dupont@example.com",
  "mdp": "MotDePasse123",
  "nom": "Dupont",
  "prenom": "Jean",
  "adresse": "12 rue de la République, 75001 Paris",
  "idRole": 2
}
```
**Response:**
```json
{
    "status": "success",
    "data": {
        "email": "jean.dupont@example.com",
        "nom": "Dupont",
        "prenom": "Jean",
        "adresse": "12 rue de la République, 75001 Paris",
        "id": 1,
        "role": "Utilisateur"
    }
}
```
# Pour avoir utilisateurs

**Url:** `GET /utilisateurs`

**Header:** `Content-Type: application/json`

**Authorization:** `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ5b3VyLWFwcCIsImF1ZCI6InlvdXItY2xpZW50IiwiaWF0IjoxNzczMjk4MTg2LjE2NjQ4OSwiZXhwIjoxNzczMzAxNzg2LjE2NjQ4OSwiZW1haWwiOiJhZG1pbkBnbWFpbC5jb20iLCJub20iOiJBZG1pbiIsInByZW5vbSI6IkFkbWluIiwiYWRyZXNzZSI6IkFua2F0c28gLCBwb3J0ZSAxMDQiLCJpZCI6MSwicm9sZSI6IkFkbWluIn0.qaMEC_5W3hgEU5fnavlRuzfZFViP22dZ-CPppZRvDjE`

**Role:**`Admin`

**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "email": "admin@gmail.com",
            "nom": "Admin",
            "prenom": "Admin",
            "adresse": "Ankatso , porte 104",
            "id": 1,
            "role": "Admin"
        }
    ]
}
```


# Pour modifier un utilisateur

**Url:** `PUT /utilisateurs/{id}`

**Header:** `Content-Type: application/json`

**Authorization:** `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ5b3VyLWFwcCIsImF1ZCI6InlvdXItY2xpZW50IiwiaWF0IjoxNzczMjk4MTg2LjE2NjQ4OSwiZXhwIjoxNzczMzAxNzg2LjE2NjQ4OSwiZW1haWwiOiJhZG1pbkBnbWFpbC5jb20iLCJub20iOiJBZG1pbiIsInByZW5vbSI6IkFkbWluIiwiYWRyZXNzZSI6IkFua2F0c28gLCBwb3J0ZSAxMDQiLCJpZCI6MSwicm9sZSI6IkFkbWluIn0.qaMEC_5W3hgEU5fnavlRuzfZFViP22dZ-CPppZRvDjE`

**Role:**`Admin`

**Body:**

```json
{
  "email": "admin@gmail.com",
  "mdp": "adminadmin",
  "nom": "Admin",
  "prenom": "Admin",
  "adresse": "Ankatso , porte 104",
  "idRole": 1
}
```
**Response:**
```json
{
    "status": "success",
    "data": {
        "email": "admin@gmail.com",
        "nom": "Admin",
        "prenom": "Admin",
        "adresse": "Ankatso , porte 104",
        "id": 1,
        "role": "Utilisateur"
    }
}
```

# Pour se loger

**Url:** `POST /utilisateurs/login`

**Header:** `Content-Type: application/json`

**Body:**

```json
{
  "email": "admin@gmail.com",
  "mdp": "adminadmin",
}
```
**Response:**
```json
{
    "status": "success",
    "data": {
        "membre": {
            "email": "admin@gmail.com",
            "nom": "Admin",
            "prenom": "Admin",
            "adresse": "Ankatso , porte 104",
            "id": 1,
            "role": "Utilisateur"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ5b3VyLWFwcCIsImF1ZCI6InlvdXItY2xpZW50IiwiaWF0IjoxNzczMjk4MTg2LjE2NjQ4OSwiZXhwIjoxNzczMzAxNzg2LjE2NjQ4OSwiZW1haWwiOiJhZG1pbkBnbWFpbC5jb20iLCJub20iOiJBZG1pbiIsInByZW5vbSI6IkFkbWluIiwiYWRyZXNzZSI6IkFua2F0c28gLCBwb3J0ZSAxMDQiLCJpZCI6MSwicm9sZSI6IkFkbWluIn0.qaMEC_5W3hgEU5fnavlRuzfZFViP22dZ-CPppZRvDjE"
    }
}
```

# Pour creer une nouvelle courrier

**Url:** `POST /courriers`

**Header:** `Content-Type: application/json`

**Authorization:** `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ5b3VyLWFwcCIsImF1ZCI6InlvdXItY2xpZW50IiwiaWF0IjoxNzczMjk4MTg2LjE2NjQ4OSwiZXhwIjoxNzczMzAxNzg2LjE2NjQ4OSwiZW1haWwiOiJhZG1pbkBnbWFpbC5jb20iLCJub20iOiJBZG1pbiIsInByZW5vbSI6IkFkbWluIiwiYWRyZXNzZSI6IkFua2F0c28gLCBwb3J0ZSAxMDQiLCJpZCI6MSwicm9sZSI6IkFkbWluIn0.qaMEC_5W3hgEU5fnavlRuzfZFViP22dZ-CPppZRvDjE`

**Role:**`Tous les utilisateurs`

**Body:**

```json
{
  "object": "Demande d'information",
  "description": "Je souhaite obtenir des informations concernant les démarches administratives pour une inscription.",
  "email": "jean.dupont@example.com",
  "nom": "Dupont",
  "prenom": "Jean",
  "telephone": "+261341234567"
}
```
**Response:**
```json
{
    "status": "success",
    "data": {
        "reference": "12032026/REF1",
        "object": "Demande d'information",
        "description": "Je souhaite obtenir des informations concernant les démarches administratives pour une inscription.",
        "email": "jean.dupont@example.com",
        "nom": "DUPONT",
        "prenom": "Jean",
        "telephone": "+261341234567",
        "dateMessage": null,
        "cloturePar": null,
        "id": 5
    }
}
```

# Pour avoir tous les  courriers disponible

**Url:** `GET /courriers`

**Header:** `Content-Type: application/json`

**Authorization:** `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ5b3VyLWFwcCIsImF1ZCI6InlvdXItY2xpZW50IiwiaWF0IjoxNzczMjk4MTg2LjE2NjQ4OSwiZXhwIjoxNzczMzAxNzg2LjE2NjQ4OSwiZW1haWwiOiJhZG1pbkBnbWFpbC5jb20iLCJub20iOiJBZG1pbiIsInByZW5vbSI6IkFkbWluIiwiYWRyZXNzZSI6IkFua2F0c28gLCBwb3J0ZSAxMDQiLCJpZCI6MSwicm9sZSI6IkFkbWluIn0.qaMEC_5W3hgEU5fnavlRuzfZFViP22dZ-CPppZRvDjE`

**Role:**`Tous les utilisateurs`


**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "reference": "12032026/REF1",
            "object": "Demande d'information",
            "description": "Je souhaite obtenir des informations concernant les démarches administratives pour une inscription.",
            "nom": "DUPONT",
            "prenom": "Jean",
            "dateMessage": null,
            "id": 5
        }
    ]
}
```

# Pour avoir tous les  courriers par utilisateur

**Url:** `GET /courriers/getAllbyUser`

**Header:** `Content-Type: application/json`

**Params** `date=2026-03-13%2012:30:45`

**Authorization:** `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ5b3VyLWFwcCIsImF1ZCI6InlvdXItY2xpZW50IiwiaWF0IjoxNzczMjk4MTg2LjE2NjQ4OSwiZXhwIjoxNzczMzAxNzg2LjE2NjQ4OSwiZW1haWwiOiJhZG1pbkBnbWFpbC5jb20iLCJub20iOiJBZG1pbiIsInByZW5vbSI6IkFkbWluIiwiYWRyZXNzZSI6IkFua2F0c28gLCBwb3J0ZSAxMDQiLCJpZCI6MSwicm9sZSI6IkFkbWluIn0.qaMEC_5W3hgEU5fnavlRuzfZFViP22dZ-CPppZRvDjE`

**Role:**`Tous les utilisateurs`


**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "reference": "12032026/REF1",
            "object": "Demande d'information",
            "description": "Je souhaite obtenir des informations concernant les démarches administratives pour une inscription.",
            "email": "jean.dupont@example.com",
            "nom": "DUPONT",
            "prenom": "Jean",
            "telephone": "+261341234567",
            "dateMessage": "2026-03-12 11:55:54",
            "cloturePar": null,
            "id": 5
        }
    ]
}
```

# Pour avoir un courrier par id

**Url:** `GET /courriers/{id}`

**Header:** `Content-Type: application/json`

**Authorization:** `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ5b3VyLWFwcCIsImF1ZCI6InlvdXItY2xpZW50IiwiaWF0IjoxNzczMjk4MTg2LjE2NjQ4OSwiZXhwIjoxNzczMzAxNzg2LjE2NjQ4OSwiZW1haWwiOiJhZG1pbkBnbWFpbC5jb20iLCJub20iOiJBZG1pbiIsInByZW5vbSI6IkFkbWluIiwiYWRyZXNzZSI6IkFua2F0c28gLCBwb3J0ZSAxMDQiLCJpZCI6MSwicm9sZSI6IkFkbWluIn0.qaMEC_5W3hgEU5fnavlRuzfZFViP22dZ-CPppZRvDjE`

**Role:**`Tous les utilisateurs`


**Response:**
```json
{
    "status": "success",
    "data": {
        "reference": "12032026/REF1",
        "object": "Demande d'information",
        "description": "Je souhaite obtenir des informations concernant les démarches administratives pour une inscription.",
        "email": "jean.dupont@example.com",
        "nom": "DUPONT",
        "prenom": "Jean",
        "telephone": "+261341234567",
        "dateMessage": null,
        "cloturePar": null,
        "id": 5
    }
}
```

# Pour avoir tout les messages d'un courrier

**Url:** `GET /courriers/{id}/messages`

**Header:** `Content-Type: application/json`

**Authorization:** `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ5b3VyLWFwcCIsImF1ZCI6InlvdXItY2xpZW50IiwiaWF0IjoxNzczMjk4MTg2LjE2NjQ4OSwiZXhwIjoxNzczMzAxNzg2LjE2NjQ4OSwiZW1haWwiOiJhZG1pbkBnbWFpbC5jb20iLCJub20iOiJBZG1pbiIsInByZW5vbSI6IkFkbWluIiwiYWRyZXNzZSI6IkFua2F0c28gLCBwb3J0ZSAxMDQiLCJpZCI6MSwicm9sZSI6IkFkbWluIn0.qaMEC_5W3hgEU5fnavlRuzfZFViP22dZ-CPppZRvDjE`

**Role:**`Tous les utilisateurs`

**Params** `date=2026-03-13%2012:30:45`

**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "isReadAt": null,
            "observation": null,
            "dateValidation": null,
            "id": 14,
            "expediteur": {
                "id": 1,
                "role": "Admin"
            },
            "destinataire": {
                "email": "test@gmail.com",
                "nom": "Rakoto",
                "prenom": "Marie",
                "adresse": "Porte 103",
                "id": 2,
                "role": "Utilisateur"
            },
            "fichiers": [
                {
                    "id": 16,
                    "nom": "document.pdf",
                    "type": "application/pdf",
                    "dateFin": null,
                    "createdAt": "2026-03-15 15:48:58"
                }
            ]
        }
    ]
}
```



# Pour creer une nouvelle messages

**Url:** `POST /messages`

**Header:** `Content-Type: application/json`

**Authorization:** `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ5b3VyLWFwcCIsImF1ZCI6InlvdXItY2xpZW50IiwiaWF0IjoxNzczMjk4MTg2LjE2NjQ4OSwiZXhwIjoxNzczMzAxNzg2LjE2NjQ4OSwiZW1haWwiOiJhZG1pbkBnbWFpbC5jb20iLCJub20iOiJBZG1pbiIsInByZW5vbSI6IkFkbWluIiwiYWRyZXNzZSI6IkFua2F0c28gLCBwb3J0ZSAxMDQiLCJpZCI6MSwicm9sZSI6IkFkbWluIn0.qaMEC_5W3hgEU5fnavlRuzfZFViP22dZ-CPppZRvDjE`

**Role:**`Admin`

**FormData:**
destId = 2
courrierId = 2
fichiers[] = file1.pdf
fichiers[] = file2.pdf

**Response:**
```json
{
    "status": "success",
    "data": {
        "isReadAt": null,
        "observation": null,
        "dateValidation": null,
        "id": 14,
        "courrier": {
            "reference": "12032026/REF1",
            "object": "Demande d'information",
            "description": "Je souhaite obtenir des informations concernant les démarches administratives pour une inscription.",
            "email": "jean.dupont@example.com",
            "nom": "DUPONT",
            "prenom": "Jean",
            "telephone": "+261341234567",
            "dateMessage": "2026-03-12 11:55:54",
            "cloturePar": null,
            "dateValidation": "2026-03-12 11:25:00",
            "id": 5
        },
        "expediteur": {
            "email": "admin@gmail.com",
            "mdp": "$2y$10$3dwubefybE5eYLMvPWKhvevNUvL1QnmbS8dZywtdpGXamyqj.j.rK",
            "nom": "Admin",
            "prenom": "Admin",
            "adresse": "Ankatso , porte 104",
            "id": 1,
            "role": "Admin"
        },
        "destinataire": {
            "email": "test@gmail.com",
            "mdp": "$2y$10$B72kQh97BUWvQTuXI9Pj/O6h2iW0cz6krW71z64Oc7py.X5RWG7Ee",
            "nom": "Rakoto",
            "prenom": "Marie",
            "adresse": "Porte 103",
            "id": 2,
            "role": "Utilisateur"
        },
        "fichiers": []
    }
}

```

# Pour transferer un message

**Url:** `POST /messages/transferer`

**Header:** `Content-Type: application/json`

**Authorization:** `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ5b3VyLWFwcCIsImF1ZCI6InlvdXItY2xpZW50IiwiaWF0IjoxNzczMjk4MTg2LjE2NjQ4OSwiZXhwIjoxNzczMzAxNzg2LjE2NjQ4OSwiZW1haWwiOiJhZG1pbkBnbWFpbC5jb20iLCJub20iOiJBZG1pbiIsInByZW5vbSI6IkFkbWluIiwiYWRyZXNzZSI6IkFua2F0c28gLCBwb3J0ZSAxMDQiLCJpZCI6MSwicm9sZSI6IkFkbWluIn0.qaMEC_5W3hgEU5fnavlRuzfZFViP22dZ-CPppZRvDjE`

**Role:**`Admin`

**FormData:**
destId = 2
id = 2
fichiers[] = file1.pdf
fichiers[] = file2.pdf

**Response:**
```json
{
    "status": "success",
    "data": {
        "isReadAt": null,
        "observation": null,
        "dateValidation": null,
        "id": 14,
        "courrier": {
            "reference": "12032026/REF1",
            "object": "Demande d'information",
            "description": "Je souhaite obtenir des informations concernant les démarches administratives pour une inscription.",
            "email": "jean.dupont@example.com",
            "nom": "DUPONT",
            "prenom": "Jean",
            "telephone": "+261341234567",
            "dateMessage": "2026-03-12 11:55:54",
            "cloturePar": null,
            "dateValidation": "2026-03-12 11:25:00",
            "id": 5
        },
        "expediteur": {
            "email": "admin@gmail.com",
            "mdp": "$2y$10$3dwubefybE5eYLMvPWKhvevNUvL1QnmbS8dZywtdpGXamyqj.j.rK",
            "nom": "Admin",
            "prenom": "Admin",
            "adresse": "Ankatso , porte 104",
            "id": 1,
            "role": "Admin"
        },
        "destinataire": {
            "email": "test@gmail.com",
            "mdp": "$2y$10$B72kQh97BUWvQTuXI9Pj/O6h2iW0cz6krW71z64Oc7py.X5RWG7Ee",
            "nom": "Rakoto",
            "prenom": "Marie",
            "adresse": "Porte 103",
            "id": 2,
            "role": "Utilisateur"
        },
        "fichiers": []
    }
}

```


# Pour marquer un message comme lu

**Url:** `PATCH /messages/{id}/lire`

**Header:** `Content-Type: application/json`

**Authorization:** `Bearer <votre_token_jwt>`

**Role:** `Utilisateur` ou `Admin`

**Body:** Aucune donnée à envoyer

**Response:**
```json
{
    "status": "success",
    "data": {
        "id": 14,
        "isReadAt": "2026-03-12 12:05:23",
        "observation": null,
        "dateValidation": null,
        "courrier": {
            "reference": "12032026/REF1",
            "object": "Demande d'information",
            "description": "Je souhaite obtenir des informations concernant les démarches administratives pour une inscription.",
            "email": "jean.dupont@example.com",
            "nom": "DUPONT",
            "prenom": "Jean",
            "telephone": "+261341234567",
            "dateMessage": "2026-03-12 11:55:54",
            "cloturePar": null,
            "dateValidation": "2026-03-12 11:25:00",
            "id": 5
        },
        "expediteur": {
            "email": "admin@gmail.com",
            "nom": "Admin",
            "prenom": "Admin",
            "id": 1,
            "role": "Admin"
        },
        "destinataire": {
            "email": "test@gmail.com",
            "nom": "Rakoto",
            "prenom": "Marie",
            "id": 2,
            "role": "Utilisateur"
        },
        "fichiers": []
    }
}
```

# Pour marquer un message comme non lu

**Url:** `PATCH /messages/{id}/non-lu`

**Header:** `Content-Type: application/json`

**Authorization:** `Bearer <votre_token_jwt>`

**Role:** `Utilisateur` ou `Admin`

**Body:** Aucune donnée à envoyer

**Response:**
```json
{
    "status": "success",
    "data": {
        "id": 14,
        "isReadAt": "2026-03-12 12:05:23",
        "observation": null,
        "dateValidation": null,
        "courrier": {
            "reference": "12032026/REF1",
            "object": "Demande d'information",
            "description": "Je souhaite obtenir des informations concernant les démarches administratives pour une inscription.",
            "email": "jean.dupont@example.com",
            "nom": "DUPONT",
            "prenom": "Jean",
            "telephone": "+261341234567",
            "dateMessage": "2026-03-12 11:55:54",
            "cloturePar": null,
            "dateValidation": "2026-03-12 11:25:00",
            "id": 5
        },
        "expediteur": {
            "email": "admin@gmail.com",
            "nom": "Admin",
            "prenom": "Admin",
            "id": 1,
            "role": "Admin"
        },
        "destinataire": {
            "email": "test@gmail.com",
            "nom": "Rakoto",
            "prenom": "Marie",
            "id": 2,
            "role": "Utilisateur"
        },
        "fichiers": []
    }
}

```

# Pour telecharger un fichier

**Url:** `GET /fichiers/{id}/download`

**Header:** `Content-Type: application/json`

**Authorization:** `Bearer <votre_token_jwt>`

**Role:**`Tous les utilisateurs`

**Response:**

Retourne le contenu binaire brut du fichier (non JSON) avec les headers :
- `Content-Type: image/jpeg` (selon le type MIME du fichier)
- `Content-Disposition: attachment; filename="nom_du_fichier.jpg"`

> L'`id` du fichier est obtenu depuis le champ `fichiers` de `GET /courriers/{id}/messages`.

**Response (erreur):**
```json
{
    "status": "error",
    "message": "Fichier introuvable."
}
```

# Pour recherche des courriers

**Url:** `POST /courriers/recherche`

**Header:** `Content-Type: application/json`

**Authorization:** `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ5b3VyLWFwcCIsImF1ZCI6InlvdXItY2xpZW50IiwiaWF0IjoxNzczMjk4MTg2LjE2NjQ4OSwiZXhwIjoxNzczMzAxNzg2LjE2NjQ4OSwiZW1haWwiOiJhZG1pbkBnbWFpbC5jb20iLCJub20iOiJBZG1pbiIsInByZW5vbSI6IkFkbWluIiwiYWRyZXNzZSI6IkFua2F0c28gLCBwb3J0ZSAxMDQiLCJpZCI6MSwicm9sZSI6IkFkbWluIn0.qaMEC_5W3hgEU5fnavlRuzfZFViP22dZ-CPppZRvDjE`

**Role:**`All role`

**Body:**

```json
{
  "reference": "REF2024",
  "object": "demande",
  "nom": "MA",
  "prenom": "Jean",
  "email": "test@mail.com",
  "telephone": "0340000000",
  "utilisateurId": 1,
  "isSend": true,
  "numero": 10,
  "dateDebut": "2024-01-01",
  "dateFin": "2026-03-15",
  "statut": "finalise"
}
```
**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "utilisateurId": 1,
            "isSend": true,
            "reference": "13032026/REF1",
            "object": "test api",
            "description": "dwfwdw",
            "email": "tambatra@gmail.com",
            "nom": "MARIE",
            "prenom": "Jean Paul",
            "telephone": null,
            "dateMessage": "2026-03-14 10:59:37",
            "cloturePar": null,
            "numero": null,
            "dateValidation": null,
            "id": 8,
            "createdAt": "2026-03-13 15:35:21",
            "createur": {
                "email": "admin@gmail.com",
                "nom": "Admin",
                "prenom": "Admin",
                "adresse": "Ankatso , porte 104",
                "id": 1,
                "createdAt": "2026-03-12 09:29:39",
                "role": "Admin"
            },
            "statut": "en_cours"
        }
    ]
}
```
