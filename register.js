// Import Firebase modules from CDN
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
import { 
  getAuth, 
  createUserWithEmailAndPassword, 
  signInWithEmailAndPassword 
} from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";
import { 
  getDatabase, 
  ref, 
  set 
} from "https://www.gstatic.com/firebasejs/10.7.1/firebase-database.js";

// Firebase configuration
const firebaseConfig = {
  apiKey: "AIzaSyAjlo6e2I1zGwtIk2SEgf-PeOs2RSf-exg",
  authDomain: "mindful-72943.firebaseapp.com",
  projectId: "mindful-72943",
  storageBucket: "mindful-72943.firebasestorage.app",
  messagingSenderId: "618540501527",
  appId: "1:618540501527:web:affa151d309feaa3ee6a52",
  databaseURL: "https://mindful-72943-default-rtdb.firebaseio.com"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const database = getDatabase(app);

// Sign Up Function
async function signUp(event) {
  event.preventDefault();
  console.log("Sign Up Form Submitted");

  try {
    // Collect form data
    const name = document.getElementById("name1").value;
    const email = document.getElementById("email2").value;
    const password = document.getElementById("pass2").value;
    const age = document.getElementById("age").value;
    const gender = event.target.querySelectorAll('select')[0].value;
    const interests = event.target.querySelector('input[placeholder="e.g., Reading, Photography, Coding"]').value;
    const designation = event.target.querySelectorAll('select')[1].value;
    const shortTermGoals = event.target.querySelectorAll('textarea')[0].value;
    const longTermGoals = event.target.querySelectorAll('textarea')[1].value;

    console.log("Form Data Collected:", { name, email, password, age, gender, interests, designation, shortTermGoals, longTermGoals });

    // Create user with email and password
    const userCredential = await createUserWithEmailAndPassword(auth, email, password);
    const user = userCredential.user;
    console.log("User Created:", user);

    // Store additional user information in Realtime Database
    await set(ref(database, 'users/' + user.uid), {
      name: name,
      email: email,
      age: age,
      gender: gender,
      interests: interests,
      designation: designation,
      shortTermGoals: shortTermGoals,
      longTermGoals: longTermGoals,
      createdAt: new Date().toISOString()
    });
    console.log("User Data Stored in Database");

    // Show success message and redirect
    window.location.href = 'dashboard.html';
  } catch (error) {
    console.error('Sign Up Error:', error.code, error.message);

  }
}

// Sign In Function
async function signIn(event) {
  event.preventDefault();
  console.log("Sign In Form Submitted");

  try {
    const email = document.getElementById("email1").value;
    const password = document.getElementById("pass1").value;
    console.log("Form Data Collected:", { email, password });

    const userCredential = await signInWithEmailAndPassword(auth, email, password);
    const user = userCredential.user;
    console.log("User Signed In:", user);

    // Show success message and redirect
    window.location.href = 'home.html';
  } catch (error) {   
    console.error('Sign In Error:', error.code, error.message);
  }
}

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', () => {
  console.log("DOM Loaded");

  // Add event listeners to forms
  const signUpForm = document.querySelector('.sign-up-form');
  const signInForm = document.querySelector('.sign-in-form');

  if (signUpForm) {
    signUpForm.addEventListener('submit', signUp);
    console.log("Sign Up Form Event Listener Added");
  }

  if (signInForm) {
    signInForm.addEventListener('submit', signIn);
    console.log("Sign In Form Event Listener Added");
  }
});