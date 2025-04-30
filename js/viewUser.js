const userCard = document.getElementById("userDetails");
const modal = document.getElementById("viewUser");
const cancelBtn = document.getElementById("cancelviewUser");

// Input fields inside the modal
const emailInput = document.getElementById("userEmail");
const lastLoginInput = document.getElementById("dateToday");
const goalList = document.getElementById("userGoalsList");
const totalSavings = document.getElementById("userTSavings");

// Simulated user data (you can replace this with dynamic data from backend)
const userData = {
email: "mothergaile@gmail.com",
lastLogin: "2025-04-30", // format: yyyy-mm-dd
goal: "Trip to Canada",
savings: "P 30,000.00"
};

// When the user card is clicked
userCard.addEventListener("click", () => {
// Fill in modal fields
emailInput.value = userData.email;
lastLoginInput.value = userData.lastLogin;
goalList.innerHTML = `<h3>${userData.goal}</h3>`;
totalSavings.textContent = userData.savings;

// Show the modal
modal.style.display = "block";
});

// Close the modal when cancel button is clicked
cancelBtn.addEventListener("click", () => {
modal.style.display = "none";
});

// Optional: Close modal when clicking outside of it
window.addEventListener("click", (e) => {
if (e.target === modal) {
    modal.style.display = "none";
}
});