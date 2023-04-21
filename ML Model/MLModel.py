import numpy as np
from pdb import set_trace
from sklearn.decomposition import PCA
import pandas as pd
from pathlib import Path

import torch.nn as nn
import torch

# Returns a column at index i from a 2D Python list
def column(matrix, i):
    return [row[i] for row in matrix]


# Takes the (file path to a folder of CSVs, user decided label for all the CSVs, and an empty list for the final processed data)     
# Each target level's CSVs need to be in their own directory, ex. dir/manicUsers/manicUser1, 2, ..., n .csv
#                                                                 dir/depressedUsers/depresseduser1, 2, ..., n .csv
#                                                                 ...

def userDataCreator(directory, label, labelList):  # labelList will be like depressedUsers = [], or manicUsers = [], etc.
    
    # Iterable of file names in a directory
    files = Path(directory).glob("*")
    
    # For loop cleans and processes each CSV in the directory
    for i in files:
        userData = pd.read_csv(i)
        userData = userData.set_index('Day/Questions')
        userData = userData.drop(userData.index[50:])
        userData = userData.drop(userData.index[[22,23,25,26,28,29,31,33,35,37,39,43]])
        userData = userData.drop(userData.iloc[:, 14:], axis=1)
        userData = userData.replace(['infinity', np.nan, 'x', 'o'], [0,0,0,0])
        userData = userData.astype(dtype="float64")
        userData = userData.transpose()
        
        # Appends tuple to labelList (processed CSV, label). labelList will be an nx2 python list.
        # where n = the number of CSVs in the target directory
        labelList.append([userData, label])
        
        # Optional print statement
        #print(labelList)
        

# Performs PCA on every processed CSV's dataframe in a targetLevel's list
def PCARow(userData):
    
    pca = PCA(n_components = 1)
    
    # Taking length (n) of the 1st column of a target level's nx2 list created by the userDataCreator method
    # Performing PCA on each dataframe, replaces each dataframe with its 1st principal component vector
    for i in range(len(column(userData, 0))):
        userData[i][0] = pca.fit_transform(userData[i][0])
    
    #userData = np.array(userData, dtype=object)
    # userData.shape = (len(userData), 2)
    
    return userData


# This cell along with the next three clean, process, all CSVs, put their dataframes into lists organized by target level
# and computes each of their first principal components

depressedUserData = []
userDataCreator(r'Depressed', 0, depressedUserData) #0 is for depressed
PCARow(depressedUserData)

manicUserData = []
userDataCreator(r'Manic', 1, manicUserData) #1 manic label
PCARow(manicUserData)

hypomanicUserData = []
userDataCreator(r'Hypomanic', 2, hypomanicUserData) #hypomanic label
PCARow(hypomanicUserData)

noEpisodeUserData = []
userDataCreator(r'NoEpisode', 3, noEpisodeUserData) #no episode
PCARow(noEpisodeUserData)

# Consolidate all targetLevel's principal components into one main list with all of 
# the (principal component, class label) pairs.

for i in manicUserData:
    depressedUserData.append(i)
    
for i in hypomanicUserData:
    depressedUserData.append(i)
    
for i in noEpisodeUserData:
    depressedUserData.append(i)
    
data = np.array(depressedUserData)

# Creating m x (n + 1) dataframe called df, where there are m principal components (should be 100 for 100 total CSVs)
# and n days (14), plus an additional one column for the class label for each principal component entry

df = pd.DataFrame(np.array(column(data, 0)).reshape(data.shape[0], 14))
pd.Series(np.array(column(data, 1)))
df['Target'] = pd.Series(np.array(column(data, 1)))

from sklearn.model_selection import train_test_split
train, test = train_test_split(df, test_size=0.25)

import torch

train_data = []

for i in range(len(train)):
    data = train.iloc[i, :]
    x = torch.tensor(data[0:14])
    y = torch.tensor(data['Target'])
    appending = (x,y)
    train_data.append(appending)
    
val_data = []

for i in range(len(test)):
    data = test.iloc[0, :]
    x = torch.tensor(data[0:14])
    y = torch.tensor(data['Target'])
    appending = (x,y)
    val_data.append(appending)
    
class Prodromal(nn.Module):
    def __init__(self, num_features, hidden_dim, num_classes):
        super(Prodromal, self).__init__()
        self.fc1 = nn.Linear(num_features, hidden_dim)
        self.dropout1 = nn.Dropout(p=0.8)
        self.fc2 = nn.Linear(hidden_dim, hidden_dim) #used to be hidden dim
        self.dropout2 = nn.Dropout(p=0.5)
        self.fc3 = nn.Linear(hidden_dim, hidden_dim)
        self.dropout3 = nn.Dropout(p=0.3)
        self.fc4 = nn.Linear(hidden_dim, num_classes)
        self.relu = nn.ReLU()

        # Xavier initialization for each linear layer
        nn.init.xavier_uniform_(self.fc1.weight, gain=nn.init.calculate_gain('relu'))
        nn.init.xavier_uniform_(self.fc2.weight, gain=nn.init.calculate_gain('relu'))
        nn.init.xavier_uniform_(self.fc3.weight, gain=nn.init.calculate_gain('relu'))
        nn.init.xavier_uniform_(self.fc4.weight, gain=1.0)

    def forward(self, x):
        #set_trace()
        x = self.fc1(x)
        x = self.relu(x)
        x = self.dropout1(x)
        x = self.fc2(x)
        x = self.relu(x)
        x = self.dropout2(x)
        x = self.fc3(x)
        x = self.relu(x)
        x = self.dropout3(x)
        x = self.fc4(x)
        x = torch.softmax(x, dim=1)  # use softmax as the activation function for the final layer
        return x
    
import torch
from torch.utils.data import DataLoader
from pdb import set_trace

batch_size = 20
num_features = 14
num_classes = 4

# Define your data samplers and loaders for training and validation sets
train_loader = DataLoader(train_data, batch_size=batch_size, shuffle=True)
val_loader = DataLoader(val_data, batch_size=batch_size, shuffle=True)

# Define your model and optimizer
model = Prodromal(num_features=num_features, hidden_dim=5, num_classes=num_classes)
loss_fn = nn.CrossEntropyLoss()
optimizer = torch.optim.Adam(model.parameters(), lr=0.1)

best_val_loss = 10000000000

# Train your model and validate
for epoch in range(100):
    # Train
    total_correct = 0
    total_loss = 0.0
    for batch in train_loader:
        inputs, labels = batch
        inputs = inputs.float()
        labels = labels.long()
        
        set_trace()

        # Forward pass
        outputs = model(inputs)

        # Compute the loss
        loss = loss_fn(outputs, labels)
        total_loss += loss.item()

        # Backward pass and optimize
        optimizer.zero_grad()
        loss.backward()
        optimizer.step()
        
        #set_trace()

        # Compute the accuracy
        _, predicted = torch.max(outputs.data, 1)
        total_correct += (predicted == labels).sum().item()

    train_loss = total_loss / len(train_loader.dataset) # gets loss of the batch
    train_acc = total_correct / len(train_loader.dataset) # gets total correct divided by total labels

    # Print train metrics
    print(f'Epoch {epoch+1}: train_loss={train_loss} train_acc={train_acc}')

    # Validate
    total_correct = 0
    total_loss = 0.0
    with torch.no_grad():
        for batch in val_loader:
            inputs, labels = batch
            
            inputs = inputs.float()
            labels = labels.long()

            # Forward pass
            outputs = model(inputs)
            
            #set_trace()

            # Forward pass
            outputs = model(inputs)
            #labels = torch.reshape(labels, (outputs.shape))

            # Compute the loss
            loss = loss_fn(outputs, labels)
            total_loss += loss.item()
            _, predicted = torch.max(outputs.data, 1)

            # Compute the accuracy
            total_correct += (predicted == labels).sum().item()
    val_loss = total_loss / len(val_loader) #gets loss of the batch
    val_acc = total_correct / len(val_data) #gets total correct divided by total labels
    if val_loss < best_val_loss:
        best_val_loss = val_loss
        best_model_state_dict = model
    # Print validation metrics
    print(f'Epoch {epoch+1}: val_loss={val_loss} val_acc={val_acc}')
# Save the best model checkpoint
torch.save(best_model_state_dict, 'best_model1.pt')