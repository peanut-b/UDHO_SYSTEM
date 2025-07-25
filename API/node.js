const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');

const app = express();
const PORT = 3001;

// Middleware
app.use(cors());
app.use(bodyParser.json());

// Secret key for JWT
const JWT_SECRET = 'your_jwt_secret_key';

// Mock database of users
const users = [
  {
    id: 1,
    username: 'admin',
    password: '$2b$10$X5Zz7QJ9m4dL3h2V1wWZNuW9XkYy1rA0bB2cC3dE4fG5hI6jK7L8', // hashed "admin123"
    role: 'Admin',
    name: 'System Administrator'
  },
  {
    id: 2,
    username: 'operation',
    password: '$2b$10$X5Zz7QJ9m4dL3h2V1wWZNuW9XkYy1rA0bB2cC3dE4fG5hI6jK7L8', // hashed "operation123"
    role: 'Operation',
    name: 'Operation Staff'
  },
  {
    id: 3,
    username: 'executive',
    password: '$2b$10$X5Zz7QJ9m4dL3h2V1wWZNuW9XkYy1rA0bB2cC3dE4fG5hI6jK7L8', // hashed "executive123"
    role: 'Admin Executive',
    name: 'Admin Executive'
  },
  {
    id: 4,
    username: 'hoa',
    password: '$2b$10$X5Zz7QJ9m4dL3h2V1wWZNuW9XkYy1rA0bB2cC3dE4fG5hI6jK7L8', // hashed "hoa123"
    role: 'HOA',
    name: 'HOA Representative'
  }
];

// API endpoint to get available roles
app.get('/api/roles', (req, res) => {
  const roles = ['Admin', 'Operation', 'Admin Executive', 'HOA'];
  res.json(roles);
});

// Login API endpoint
app.post('/api/login', async (req, res) => {
  const { username, password, role } = req.body;

  // Find user by username and role
  const user = users.find(u => u.username === username && u.role === role);

  if (!user) {
    return res.status(401).json({ message: 'Invalid credentials or role' });
  }

  // Compare passwords
  const isMatch = await bcrypt.compare(password, user.password);
  
  if (!isMatch) {
    return res.status(401).json({ message: 'Invalid credentials' });
  }

  // Create JWT token
  const token = jwt.sign(
    { userId: user.id, username: user.username, role: user.role, name: user.name },
    JWT_SECRET,
    { expiresIn: '1h' }
  );

  res.json({ 
    token,
    user: {
      id: user.id,
      username: user.username,
      role: user.role,
      name: user.name
    }
  });
});

// Protected route example
app.get('/api/protected', authenticateToken, (req, res) => {
  res.json({ message: `Welcome ${req.user.name} (${req.user.role})`, user: req.user });
});

// Middleware to authenticate JWT token
function authenticateToken(req, res, next) {
  const authHeader = req.headers['authorization'];
  const token = authHeader && authHeader.split(' ')[1];

  if (!token) {
    return res.status(401).json({ message: 'No token provided' });
  }

  jwt.verify(token, JWT_SECRET, (err, user) => {
    if (err) {
      return res.status(403).json({ message: 'Invalid or expired token' });
    }
    req.user = user;
    next();
  });
}

app.listen(PORT, () => {
  console.log(`Server running on http://localhost:${PORT}`);
});