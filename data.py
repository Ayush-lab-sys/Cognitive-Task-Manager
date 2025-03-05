import csv
import random
from faker import Faker

fake = Faker()

# Define categories and their task templates
categories = {
    "Work": [
        "Prepare slides for {meeting}",
        "Submit {report_type} report",
        "Email {client} about {project}",
        "Attend {meeting} at {time}",
    ],
    "Personal": [
        "Call {name} for {event}",
        "Organize {item}",
        "Update personal calendar",
    ],
    "Shopping": [
        "Buy {item1} and {item2}",
        "Order {product} online",
        "Pick up groceries from {store}",
    ],
    "Fitness": [
        "Go for a {duration}-minute {activity}",
        "Attend {fitness_class} class",
        "Do {exercise} reps",
    ],
    "Education": [
        "Read chapter {chapter} of {book}",
        "Complete {subject} assignment",
        "Study for {exam} exam",
    ],
    "Home": [
        "Fix the {item} in the {room}",
        "Clean the {room}",
        "Organize {item} in the {room}",
    ],
    "Health": [
        "Schedule a {appointment_type} appointment",
        "Take {medication} at {time}",
        "Go for a {health_activity}",
    ],
    "Finance": [
        "Review monthly budget",
        "Pay {bill} bill",
        "Invest in {investment_type}",
    ],
    "Travel": [
        "Plan trip to {destination}",
        "Book {transport} tickets",
        "Pack {item} for the trip",
    ],
    "Social": [
        "Meet {name} for {event}",
        "Attend {event} at {location}",
        "Invite {name} to {event}",
    ],
    "Entertainment": [
        "Watch {movie} on {platform}",
        "Play {game} with {name}",
        "Listen to {music_artist}",
    ],
    "Cooking": [
        "Cook {dish} for {meal}",
        "Bake {item} for {event}",
        "Try a new {cuisine} recipe",
    ],
    "Family": [
        "Help {name} with {help_task}",  # Changed placeholder from {task} to {help_task}
        "Plan {event} for {name}",
        "Spend time with {name}",
    ],
    "Pets": [
        "Walk {pet} in the park",
        "Feed {pet} at {time}",
        "Take {pet} to the vet",
    ],
    "Other": [
        "Research {topic}",
        "Learn {skill}",
        "Explore {interest}",
    ],
}

# Generate 5000 tasks
tasks = []
for _ in range(5000):
    category = random.choice(list(categories.keys()))
    template = random.choice(categories[category])
    
    # Fill template with fake data
    task = template.format(
        meeting=fake.bs(),
        report_type=fake.word(),
        client=fake.company(),
        project=fake.catch_phrase(),
        time=fake.time(),
        name=fake.first_name(),
        event=fake.word(),
        item=fake.word(),
        item1=fake.word(),
        item2=fake.word(),
        product=fake.word(),
        store=fake.company(),
        duration=random.randint(10, 60),
        activity=random.choice(["jog", "walk", "swim", "cycle"]),
        fitness_class=random.choice(["yoga", "pilates", "Zumba", "HIIT"]),
        exercise=random.choice(["push-up", "squat", "pull-up", "plank"]),
        chapter=random.randint(1, 20),
        book=fake.catch_phrase(),
        subject=fake.word(),
        exam=fake.word(),
        room=fake.word(),
        appointment_type=random.choice(["dentist", "doctor", "therapy"]),
        medication=fake.word(),
        health_activity=random.choice(["yoga", "meditation", "walk"]),
        bill=fake.word(),
        investment_type=random.choice(["stocks", "bonds", "mutual funds"]),
        destination=fake.city(),
        transport=random.choice(["flight", "train", "bus"]),
        location=fake.city(),
        movie=fake.word(),
        platform=random.choice(["Netflix", "Hulu", "Disney+"]),
        game=fake.word(),
        music_artist=fake.name(),
        dish=fake.word(),
        meal=random.choice(["breakfast", "lunch", "dinner"]),
        cuisine=fake.word(),
        pet=fake.first_name(),
        topic=fake.word(),
        skill=fake.word(),
        interest=fake.word(),
        help_task=fake.word(),  # Added to replace {task} in the Family category
    )
    
    tasks.append([task, category])

# Save to CSV
with open("task_dataset.csv", "w", newline="", encoding="utf-8") as f:
    writer = csv.writer(f)
    writer.writerow(["task", "category"])
    writer.writerows(tasks)

print("Dataset generated: task_dataset.csv")