# QODEX - Plateforme de Quiz

## 📖 Description
Application web de quiz permettant aux enseignants de créer des quiz et aux étudiants de les passer.

## 🚀 Technologies
- PHP 7+
- MySQL
- TailwindCSS
- JavaScript

## ⚡ Installation

1. Cloner le projet
2. Configurer la base de données dans `config/database.php`
3. Créer la base `qodex_v1`
4. Lancer l'application

## 🔑 Comptes de test

**Enseignant**
- Email: `ahmed@enseignant.com`
- Mot de passe: `Test123456`

**Étudiant**
- Email: `youssef@etudiant.com`
- Mot de passe: `Test123456`

## 📁 Structure
```
qodex/
├── auth/          # Authentification
├── enseignant/    # Espace enseignant
├── etudiant/      # Espace étudiant
└── config/        # Configuration
```

## ✨ Fonctionnalités

### Enseignants
- Créer des catégories et quiz
- Gérer les questions
- Consulter les résultats

### Étudiants
- Passer des quiz
- Voir ses résultats
- Suivre sa progression

## 🔒 Sécurité
- Protection CSRF
- Hashage bcrypt
- Sessions sécurisées

---
Made with ❤️ for education