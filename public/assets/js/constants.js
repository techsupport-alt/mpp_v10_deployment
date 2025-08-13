/* ====================================== */
/* APPLICATION CONSTANTS & DATA */
/* Static data for the MPP Application */
/* ====================================== */

/**
 * Testimonials data for the slider component
 */
export const TESTIMONIALS = [
    {
        id: 1,
        quote: "During the 83-hour marathon praise, I witnessed the most powerful move of God in my life. Chains were broken, healing manifested, and my family was completely restored. This movement is changing Nigeria!",
        name: "Pastor Sarah Adebayo",
        title: "Lagos State Coordinator",
        color: "orange"
    },
    {
        id: 2,
        quote: "The prayer coverage across Nigeria has been unprecedented. In our state alone, we've seen over 200 salvations and countless miracles. God is moving mightily through this 84-day movement!",
        name: "Evangelist John Okafor",
        title: "Anambra State Leader",
        color: "brown"
    },
    {
        id: 3,
        quote: "My barrenness of 12 years was broken during the Day 3 Reach 4 Christ prayers. Today, I carry a testimony of God's faithfulness. Every prayer warrior needs to be part of this movement!",
        name: "Mrs. Grace Yakubu",
        title: "Prayer Partner, Kaduna",
        color: "orange"
    }
];

/**
 * FAQ data for the accordion component
 */
export const FAQS = [
    {
        id: 'what-is-mpp',
        question: 'What is Marathon Praise & Prayer (MPP)?',
        answer: 'MPP is a 84-day continuous prayer and worship movement calling believers across Nigeria to join in 24/7 intercession for national transformation and spiritual awakening. It brings together churches, individuals, and communities in unified prayer for revival.',
        category: 'General'
    },
    {
        id: 'when-does-event-start',
        question: 'When does the 84-day marathon start?',
        answer: 'The 84-day Marathon Praise & Prayer officially begins on [Start Date] and runs continuously for 84 days until [End Date]. Each day features multiple prayer sessions and worship gatherings across the nation.',
        category: 'Schedule'
    },
    {
        id: 'who-can-participate',
        question: 'Who can participate in MPP?',
        answer: 'Everyone is welcome! MPP is open to all believers regardless of denomination, age, or location. Whether you\'re an individual, part of a church, or leading a community group, you can join this movement of prayer and worship.',
        category: 'Participation'
    },
    {
        id: 'how-to-join',
        question: 'How can I join the prayer sessions?',
        answer: 'You can join through multiple ways: attend physical gatherings in your area, participate in online prayer sessions, sign up for a specific prayer time slot, or join the 24/7 prayer chain from your location.',
        category: 'Participation'
    },
    {
        id: 'volunteer-opportunities',
        question: 'What volunteer opportunities are available?',
        answer: 'We have various volunteer roles including prayer coordination, event organization, social media support, technical assistance, worship team participation, and local community mobilization. Each role contributes to the success of this movement.',
        category: 'Volunteering'
    },
    {
        id: 'prayer-points',
        question: 'What can I expect from the daily prayer points?',
        answer: 'Daily prayer points focus on national transformation, spiritual awakening, government leaders, economic breakthrough, security, unity, and church revival. These are distributed through our newsletter, website, and social media channels.',
        category: 'Prayer'
    }
];

/**
 * Latest updates data for the updates section
 */
export const UPDATES = [
    {
        id: 1,
        type: 'Announcement',
        title: '84-Day Marathon Officially Begins',
        content: 'The nationwide prayer and worship movement has officially commenced with thousands of believers joining from all 36 states of Nigeria. The opening ceremony witnessed unprecedented unity and divine presence.',
        date: '2 days ago',
        views: '2.5K views',
        icon: 'megaphone',
        color: 'orange'
    },
    {
        id: 2,
        type: 'Testimony',
        title: 'Miraculous Healing in Kano',
        content: 'During the Day 2 prayer session, a woman with chronic illness for 15 years received instant healing. Medical reports confirm complete restoration, bringing glory to God and strengthening faith across the region.',
        date: '3 days ago',
        views: '1.8K views',
        icon: 'heart',
        color: 'brown'
    },
    {
        id: 3,
        type: 'Event',
        title: 'Special Youth Prayer Night',
        content: 'Join young believers across Nigeria for a dedicated night of prayer and worship. This special session focuses on the future of our nation and empowering the next generation for God\'s purposes.',
        date: '5 days ago',
        views: '3.2K interested',
        icon: 'calendar',
        color: 'orange'
    },
    {
        id: 4,
        type: 'Report',
        title: 'Prayer Coverage Reaches 90%',
        content: 'Amazing response from believers nationwide! We\'ve achieved 90% prayer coverage across all time slots, ensuring continuous intercession for Nigeria. Join us to reach 100% coverage.',
        date: '1 week ago',
        views: 'Live data',
        icon: 'trending-up',
        color: 'brown'
    },
    {
        id: 5,
        type: 'Global',
        title: 'Diaspora Joins the Movement',
        content: 'Nigerians in the diaspora are organizing local prayer gatherings in over 50 countries. From USA to UK, Canada to Germany, believers worldwide are standing with Nigeria in prayer.',
        date: '1 week ago',
        views: '50+ countries',
        icon: 'globe',
        color: 'orange'
    },
    {
        id: 6,
        type: 'Resources',
        title: 'Daily Prayer Guide Released',
        content: 'Download the comprehensive 84-day prayer guide with specific prayer points, scripture declarations, and worship songs for each day of the movement. Available in English, Hausa, Igbo, and Yoruba.',
        date: '2 weeks ago',
        views: '15K downloads',
        icon: 'book-open',
        color: 'brown'
    }
];

/**
 * Nigerian states for form dropdowns
 */
export const NIGERIAN_STATES = [
    'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue', 'Borno',
    'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'FCT', 'Gombe', 'Imo',
    'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara', 'Lagos',
    'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo', 'Plateau', 'Rivers',
    'Sokoto', 'Taraba', 'Yobe', 'Zamfara'
];

/**
 * Volunteer areas of interest
 */
export const VOLUNTEER_AREAS = [
    { value: 'prayer_coordination', label: 'Prayer Coordination' },
    { value: 'event_planning', label: 'Event Planning' },
    { value: 'social_media', label: 'Social Media' },
    { value: 'technical_support', label: 'Technical Support' },
    { value: 'worship_team', label: 'Worship Team' },
    { value: 'mobilization', label: 'Community Mobilization' }
];

/**
 * Availability options for volunteers
 */
export const AVAILABILITY_OPTIONS = [
    { value: 'full_time', label: 'Full Time (40+ hours/week)' },
    { value: 'part_time', label: 'Part Time (20-40 hours/week)' },
    { value: 'flexible', label: 'Flexible (10-20 hours/week)' },
    { value: 'weekends_only', label: 'Weekends Only' },
    { value: 'special_events', label: 'Special Events Only' }
];

/**
 * Prayer time slots for prayer signup
 */
export const PRAYER_TIME_SLOTS = [
    { value: '12am-3am', label: '12:00 AM - 3:00 AM' },
    { value: '3am-6am', label: '3:00 AM - 6:00 AM' },
    { value: '6am-9am', label: '6:00 AM - 9:00 AM' },
    { value: '9am-12pm', label: '9:00 AM - 12:00 PM' },
    { value: '12pm-3pm', label: '12:00 PM - 3:00 PM' },
    { value: '3pm-6pm', label: '3:00 PM - 6:00 PM' },
    { value: '6pm-9pm', label: '6:00 PM - 9:00 PM' },
    { value: '9pm-12am', label: '9:00 PM - 12:00 AM' },
    { value: 'flexible', label: 'Flexible' }
];

/**
 * Configuration constants
 */
export const CONFIG = {
    TESTIMONIAL_AUTOPLAY_INTERVAL: 8000,
    ANIMATION_DELAY_INCREMENT: 0.1,
    NOTIFICATION_DURATION: 5000,
    FORM_SIMULATION_DELAY: 1000,
    DARK_MODE_STORAGE_KEY: 'mpp-dark-mode'
};