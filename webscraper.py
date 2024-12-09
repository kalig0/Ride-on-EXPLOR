import scrapy
from geopy.geocoders import Nominatim
import pandas as pd
from datetime import datetime

geolocator = Nominatim(user_agent="Geolocation")

# Dictionary to map state names to abbreviations
STATE_ABBREVIATIONS = {
    'Alabama': 'AL', 'Alaska': 'AK', 'Arizona': 'AZ', 'Arkansas': 'AR', 
    'California': 'CA', 'Colorado': 'CO', 'Connecticut': 'CT', 'Delaware': 'DE', 
    'Florida': 'FL', 'Georgia': 'GA', 'Hawaii': 'HI', 'Idaho': 'ID', 
    'Illinois': 'IL', 'Indiana': 'IN', 'Iowa': 'IA', 'Kansas': 'KS', 
    'Kentucky': 'KY', 'Louisiana': 'LA', 'Maine': 'ME', 'Maryland': 'MD', 
    'Massachusetts': 'MA', 'Michigan': 'MI', 'Minnesota': 'MN', 'Mississippi': 'MS', 
    'Missouri': 'MO', 'Montana': 'MT', 'Nebraska': 'NE', 'Nevada': 'NV', 
    'New Hampshire': 'NH', 'New Jersey': 'NJ', 'New Mexico': 'NM', 
    'New York': 'NY', 'North Carolina': 'NC', 'North Dakota': 'ND', 
    'Ohio': 'OH', 'Oklahoma': 'OK', 'Oregon': 'OR', 'Pennsylvania': 'PA', 
    'Rhode Island': 'RI', 'South Carolina': 'SC', 'South Dakota': 'SD', 
    'Tennessee': 'TN', 'Texas': 'TX', 'Utah': 'UT', 'Vermont': 'VT', 
    'Virginia': 'VA', 'Washington': 'WA', 'West Virginia': 'WV', 
    'Wisconsin': 'WI', 'Wyoming': 'WY'
}

class Spider(scrapy.Spider):
    name = 'spider'
    start_urls = [
        'https://www.bikemonkey.net/tnt', 
        'https://www.ridefishrock.com/', 
        'https://www.levisgranfondo.com/', 
        'https://www.boggs.rocks/', 
        'https://www.stetinaspaydirt.com/', 
        'https://www.racewente.com/', 
        'https://www.truckeetahoegravel.com/', 
        'https://www.climateride.org/events/wine-country/',
        'https://www.rebeccasprivateidaho.com/',
        'https://give.michaeljfox.org/event/2024-tour-de-fox-wine-country/e571796',
        'https://www.heartofgoldgravel.com/',
        'https://www.thebovineclassic.com/',
    ]
    
    def __init__(self):
        self.event_data = []

    def parse(self, response):
        # Extract event name and remove "Until " prefix if present
        event_name = response.css('#clocktitle::text').get() or ''
        if event_name.startswith("Until "):
            event_name = event_name[len("Until "):].strip()
        
        # Format the date
        raw_date = response.css('.ccp-sandbox--main-hero-section-title::text').get() or ''
        try:
            formatted_date = datetime.strptime(raw_date, '%B %d, %Y').strftime('%-m/%-d/%Y  12:00:00 AM')
        except ValueError:
            formatted_date = ''  # Default to blank if the format is unrecognized

        location = response.css('.ccp-sandbox--main-hero-section-subtitle::text').get() or ''
        
        # Extract City and State if location is of the form "City, State"
        city, state = '', ''
        if ',' in location:
            parts = location.split(',', 1)
            city = parts[0].strip()
            state_full = parts[1].strip()
            state = STATE_ABBREVIATIONS.get(state_full, state_full)  # Convert to abbreviation or keep as is
        else:
            city = location  # Fallback if only city is provided

        # Get Coordinates
        if city:
            coord = geolocator.geocode(location)
            longitude = coord.longitude if coord else ''
            latitude = coord.latitude if coord else ''
        else:
            longitude = ''
            latitude = ''

        # Append the extracted data to the event_data list
        self.event_data.append({
            "Name": event_name,
            "Start_Date": formatted_date,
            "End_Date": '',
            "Location": '',
            "City": city,
            "State": state,
            "Zip": '',  # Placeholder, extract if needed
            "Longitude": longitude,
            "Latitude": latitude,
            "Details": response.url,
            "Notes": '',
            "Type": ''
        })
    
    def closed(self, reason):
        # Convert the scraped data to a DataFrame
        df_new = pd.DataFrame(self.event_data)
        
        # Path to the existing CSV file
        existing_csv_path = 'Events.csv'
        
        try:
            # Load the existing CSV if it exists
            df_existing = pd.read_csv(existing_csv_path)
            # Append the new data to the existing data
            df_combined = pd.concat([df_existing, df_new], ignore_index=True)
        except FileNotFoundError:
            # If the file doesn't exist, use the new data only
            df_combined = df_new
        
        # Save the combined data back to the existing CSV file
        df_combined.to_csv(existing_csv_path, index=False)

